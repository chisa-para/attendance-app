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

class AdminApprovalController extends Controller
{

    public function show($id)
    {
      $approvalRequest = ApprovalRequest::with('user', 'attendance', 'approvalRests')->findOrFail($id);
      $date = $approvalRequest->attendance->start_at->format('Y年m月d日');
      $start = $approvalRequest->request_start_at->format('H:i');
      $finish = $approvalRequest->request_finish_at->format('H:i');

      $rests = $approvalRequest->approvalRests;

      $rests->push(new Rest([
        'id' => null, // 新規登録用なのでIDはnull
        'rest_start_at' => null,
        'rest_finish_at' => null,
      ]));

      return view('admin.approval',compact('approvalRequest','date','start','finish','rests','id'));
    }

    public function update($id)
    {
      return DB::transaction(function () use ($id) {
        $approvalRequest = ApprovalRequest::with('user', 'attendance', 'approvalRests')->findOrFail($id);
        $restRequests = $approvalRequest->approvalRests;
        $attendance = $approvalRequest->attendance;
        
        // 元の日付を取得
        $originalDate = Carbon::parse($attendance->start_at)->format('Y-m-d ');

        // 申請データから「時刻（H:i:s）」だけを抽出して結合する
        $reqStartAt  = $originalDate . Carbon::parse($approvalRequest->request_start_at)->format('H:i:s');
        $reqFinishAt = $originalDate . Carbon::parse($approvalRequest->request_finish_at)->format('H:i:s');
  
        $attendance->update([
          'start_at'  => $reqStartAt,
          'finish_at' => $reqFinishAt,
          'retouch_reason' => $approvalRequest->reason,
        ]);

        foreach ($restRequests as $restRequest) {
          // 各休憩申請の時刻から「時刻（H:i:s）」だけを抽出
          $restStartAt  = $originalDate . Carbon::parse($restRequest->request_rest_start_at)->format('H:i:s');
          $restFinishAt = $originalDate . Carbon::parse($restRequest->request_rest_finish_at)->format('H:i:s');
        
          if ($restRequest->rest_id) {
            $rest = Rest::findOrFail($restRequest->rest_id);
            $rest->update([
              'rest_start_at'  => $restStartAt,
              'rest_finish_at' => $restFinishAt,
            ]);
          } else {
            $newRest = Rest::create([
              'attendance_id'  => $approvalRequest->attendance_id,
              'rest_start_at'  => $restStartAt,
              'rest_finish_at' => $restFinishAt,
            ]);

            // 新しく作成した休憩のIDを申請レコードに反映
            $restRequest->rest_id = $newRest->id;
          }
          // ループ内での$restRequest->rest_id を書き換え保存
          $restRequest->save();
        }

        $approvalRequest->status = 'approved';
        $approvalRequest->save();

        return redirect('admin/attendance/list')->with('success', '申請を承認しました。');
      });
    }
}
