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
use App\Models\ApprovalRest;
use App\Http\Requests\ApprovalRequestStoreRequest;

class ApprovalRequestController extends Controller
{
    public function show($id, Request $request)
    {
        $user = Auth::user();

        $attendance = Attendance::where('id', $id)->with('rests')->first();
        $date = $attendance->start_at->format('Y年m月d日');
        $start = $attendance->start_at->format('H:i');
        $finish = $attendance->finish_at->format('H:i');

        $rests = $attendance->rests->values();

        $rests->push(new Rest([
            'id' => null, // 新規登録用なのでIDはnull
            'rest_start_at' => null,
            'rest_finish_at' => null,
        ]));

        $isPending = ApprovalRequest::where('attendance_id', $id)
                                ->where('status', 'waiting')
                                ->exists();

        $from = $request->input('from', '');
        

        return view('general.attendance_detail', compact('user','date','start','finish','attendance','rests','isPending','from'));
    }

    public function store($id, ApprovalRequestStoreRequest $request)
    {
        $attendance = Attendance::with('rests')->findOrFail($id);
        $validated = $request->validated();
        
        $approvalRequest = ApprovalRequest::create([
            'attendance_id'    => $id,
            'reason'           => $validated['reason'],
            'status'           => 'waiting',
            'request_start_at' => $validated['request_start_at'],
            'request_finish_at'=> $validated['request_finish_at'],
        ]);

    // 2. フロントから送られてきた休憩データの配列をループ処理
    // $request->rests は [ 0 => ['id' => 1, 'request_rest_start_at' => '12:00', ...], 1 => [...] ] のような構造で届きます
        if (!empty($validated['rests'])) {
            foreach ($validated['rests'] as $restData) {
            // 開始時間と終了時間の両方が入力されている場合のみ保存する（空のフォーム対策）
                if (!empty($restData['request_rest_start_at']) && !empty($restData['request_rest_finish_at'])) {
                    ApprovalRest::create([
                        'approval_request_id'   => $approvalRequest->id,
                        'rest_id'               => $restData['id'] ?? null, // 登録済みの休憩ID（新規追加用の空フォームからならnull）
                        'request_rest_start_at' => $restData['request_rest_start_at'],
                        'request_rest_finish_at'=> $restData['request_rest_finish_at'],
                    ]);
                }
            }
        }
        return redirect("/attendance/list");
    }

    public function index(Request $request)
    {
        $status = $request->input('status', 'waiting');
        $keyword = $request->input('keyword');

        // 2. 申請データをリレーションと一緒に取得（最新順）
        $query = ApprovalRequest::with(['attendance.user'])->latest();

        $query->where('status', $status);

        if(!Auth::user()->admin_status) {
        $query->whereHas('attendance', function($q) {
            $q->where('user_id', Auth::id());
        });
    }

        // 3. もしキーワード（名前など）があれば絞り込み（必要に応じて実装してください）
        if (!empty($keyword)) {
            $query->whereHas('attendance.user', function($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            });
        }

        // 4. データを取得（ページネーションにする場合は paginate(10) などに）
        $requests = $query->get();

        
        
        return view('approval_request_list', compact('requests', 'keyword'));
    }

}
