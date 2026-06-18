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


class AttendanceController extends Controller
{
    public function home()
    {
        $today = Carbon::today();
        $todayDate = Carbon::today()->format('Y年m月d日');
        $nowTime = Carbon::now()->format('H:i');

        $user = Auth::user();
        $status = 'before_work'; 

        $attendance = Attendance::where('user_id', $user->id)->whereDate('start_at', $today)->first();

        if ($attendance) {
            if ($attendance->finish_at) {
                
                $status = 'after_work';
            } else {
            
                $latestRest = $attendance->rests()->latest()->first();

                if ($latestRest && is_null($latestRest->rest_finish_at)) {
                
                    $status = 'resting';
                } else {
                
                    $status = 'working';
                }
            }
        }
        
        return view('general.attendance_record',compact('todayDate', 'nowTime','status','attendance'));
    }

    public function start(Request $request)
    {
        $user = Auth::user();

        $attendance = Attendance::create([
            'user_id'   => $user->id,
            'start_at'  => Carbon::now(),
            'finish_at' => null,
        ]);

        return redirect("/attendance");
    }

    public function finish(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)->whereDate('start_at', $today)->first();

        $attendance ->finish_at = Carbon::now();
        $attendance -> save();

        return redirect("/attendance");
    }

    public function rest(Request $request)
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();

        $rest = Rest::create([
            'attendance_id'   => $attendance->id,
            'rest_start_at'  => Carbon::now(),
            'rest_finish_at' => null,
        ]);

        return redirect("/attendance");
    }

    public function restOut(Request $request)
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();

        $rest = Rest::where('attendance_id', $attendance->id)->latest()->first();

        $rest ->rest_finish_at = Carbon::now();
        $rest -> save();

        return redirect("/attendance");
    }

    public function index(Request $request)
    {
        // 1. 対象の月を取得（指定がなければ当月）
        $monthParam = $request->input('month', Carbon::now()->format('Y-m'));
        $targetMonth = Carbon::parse($monthParam);

        // 2. ログインユーザーの指定月の勤怠データを取得
        $attendances = Attendance::with('rests')
            ->where('user_id', Auth::id())
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
            // ループ処理中の日付インスタンスを作成（Y-m-d）
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

                // 休憩時間の合計（分）
                $totalRestMinutes = $attendance->total_rest_minutes;

                // 実働時間
                $workingTimeStr = $attendance->actual_work_time;

                //休憩時間を60→01:00表記にする
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

        return view('general.attendance_list', compact('attendanceList', 'targetMonth', 'prevMonth', 'nextMonth'));
    }

    
}
