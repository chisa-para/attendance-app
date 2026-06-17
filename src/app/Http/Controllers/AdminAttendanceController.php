<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\ApprovalRequest;
use App\Http\Requests\AdminAttendanceRequest;

class AdminAttendanceController extends Controller
{
    
    public function index(Request $request)
    {
        $targetDateString = $request->input('day', Carbon::today()->toDateString());
        $targetDate = Carbon::parse($targetDateString);

        $todayDate = $targetDate->format('Y-m-d'); 
        $displayDate = $targetDate->format('Y/m/d');

        // 2. 「前の日」と「次の日」のURL用文字列を作成
        $prevDate = $targetDate->copy()->subDay()->toDateString();
        $nextDate = $targetDate->copy()->addDay()->toDateString();

        $users = User::where('admin_status',false)->with(['attendances' => function($query) use ($todayDate) {
            $query->whereDate('start_at', $todayDate)->with('rests');
        }])->get();

        return view('admin.attendance_list',compact('displayDate', 'users','prevDate','nextDate'));
    }

    public function show($id, Request $request)
    {
        $attendance = Attendance::where('id', $id)->with('user','rests')->first();
        $date = $attendance->start_at->format('Y年m月d日');
        $start = $attendance->start_at->format('H:i');
        $finish = $attendance->finish_at->format('H:i');

        $rests = $attendance->rests;

        $rests->push(new Rest([
            'id' => null, // 新規登録用なのでIDはnull
            'rest_start_at' => null,
            'rest_finish_at' => null,
        ]));

        $isPending = ApprovalRequest::where('attendance_id', $id)
                                ->where('status', 'waiting')
                                ->exists();

        return view('admin.attendance_detail', compact('date','start','finish','attendance','rests','isPending'));
    }

    public function update($id, AdminAttendanceRequest $request)
    {
        $attendance = Attendance::with('user','rests')->findOrFail($id);
        $rests = $attendance->rests;

        $validated = $request->validated();
        
        $originalDate = Carbon::parse($attendance->start_at)->format('Y-m-d');

        ApprovalRequest::create([
            'attendance_id'    => $id,
            'reason'           => $validated['reason'],
            'status'           => 'approved',
            'request_start_at' => $validated['start_at'],
            'request_finish_at'=> $validated['finish_at'],
        ]);

        $attendance->update([
            'id' => $id,
            'user_id' =>$validated['user_id'] ?? $attendance->user_id,
            'start_at' =>$originalDate.$validated['start_at'],
            'finish_at' =>$originalDate.$validated['finish_at'],
            'retouch_reason' => $validated['reason'],
        ]);

        if (!empty($validated['rests'])) {
            foreach ($validated['rests'] as $restData) {
            
                // 開始時間と終了時間の両方が入力されている場合のみ処理する（空フォーム対策）
                if (!empty($restData['rest_start_at']) && !empty($restData['rest_finish_at'])) {
                
                    Rest::updateOrCreate(
                        // 【第1引数】検索条件（既存データを探すための条件）
                        [
                            'id' => $restData['id'] ?? null // IDがあればそれを探す、無ければ必ず新規作成になる
                        ],
                        // 【第2引数】保存・更新する内容
                        [
                            'attendance_id' => $id,
                            'rest_start_at' => $originalDate . $restData['rest_start_at'],
                            'rest_finish_at'=> $originalDate . $restData['rest_finish_at'],
                        ]
                    );
                }
            }

            return redirect('admin/attendance/list');
        }
    }

    public function monthIndex($id, Request $request)
    {
        $user = User::findOrFail($id);
        $monthParam = $request->input('month', Carbon::now()->format('Y-m'));
        $targetMonth = Carbon::parse($monthParam);

        // 2. ログインユーザーの指定月の勤怠データを取得
        $attendances = Attendance::with('rests')
            ->where('user_id', $id)
            ->whereYear('start_at', $targetMonth->year)
            ->whereMonth('start_at', $targetMonth->month)
            ->get()
            // 日付（Y-m-d形式）をキーにしたコレクションに変換しておく（ここがポイント！）
            ->keyBy(function ($attendance) {
                return Carbon::parse($attendance->start_at)->format('Y-m-d');
            });

        // 3. 1日〜末日までの全日付ループ用の器（配列）を用意
        $attendanceList = [];
        $daysInMonth = $targetMonth->daysInMonth; // その月の日数（28〜31）
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            // ループ処理中の日付インスタンスを作成（例: 2026-05-01）
            $currentDate = $targetMonth->copy()->day($day);
            $dateKey = $currentDate->format('Y-m-d'); // 検索用のキー
            $dayOfWeek = $weekdays[$currentDate->dayOfWeek]; // 曜日

            // 基本的な表示形式（データがない日は空欄になる）
            $rowData = [
                'id' => '',
                'date' => $currentDate->format('m/d') . "({$dayOfWeek})",
                'start_at' => '',
                'finish_at' => '',
                'rest_time' => '',
                'working_time' => '',
                'is_weekend' => $currentDate->isWeekend(), // 土日判定（CSS装飾用）
            ];

            // 4. 【マッピング処理】DBから取得したデータ（$attendances）の中に、該当する日付のデータがあるか確認
            if ($attendances->has($dateKey)) {
                $attendance = $attendances->get($dateKey);
                $start = Carbon::parse($attendance->start_at);
                $finish = $attendance->finish_at ? Carbon::parse($attendance->finish_at) : null;

                // --- 休憩時間の合計（分）を計算 ---
                $totalRestMinutes = 0;
                foreach ($attendance->rests as $rest) {
                    if ($rest->rest_start_at && $rest->rest_finish_at) {
                        $totalRestMinutes += Carbon::parse($rest->rest_start_at)->diffInMinutes(Carbon::parse($rest->rest_finish_at));
                    }
                }

                // --- 実働時間の計算 ---
                $workingTimeStr = '-';
                if ($finish) {
                    $totalMinutes = $start->diffInMinutes($finish);
                    $actualMinutes = $totalMinutes - $totalRestMinutes;
                    if ($actualMinutes > 0) {
                        $workingTimeStr = sprintf('%02d:%02d', floor($actualMinutes / 60), $actualMinutes % 60);
                    }
                }

                $restTimeStr = sprintf('%02d:%02d', floor($totalRestMinutes / 60), $totalRestMinutes % 60);

                // 空欄だった枠に、計算したデータを上書き（マッピング）
                $rowData['id'] = $attendance->id;
                $rowData['start_at'] = $start->format('H:i');
                $rowData['finish_at'] = $finish ? $finish->format('H:i') : '勤怠中';
                $rowData['rest_time'] = $restTimeStr;
                $rowData['working_time'] = $workingTimeStr;
            }

            // 配列に追加
            $attendanceList[] = $rowData;
        }

        // 前月・翌月のリンク用
        $prevMonth = $targetMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetMonth->copy()->addMonth()->format('Y-m');

        return view('admin.each_attendance_list', compact('user','attendanceList', 'targetMonth', 'prevMonth', 'nextMonth'));
    }
}