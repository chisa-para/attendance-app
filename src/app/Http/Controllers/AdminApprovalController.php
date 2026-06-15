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

        return view('admin.approval',compact('approvalRequest','date','start','finish','rests'));
    }

    //public function update($id,ApprovalRequest $request)
    //{
      //  $attendance = Attendance::where('id',$id)->with('user','rests');
       // $rests = $attendance->rests;

//        $model = Attendance::find($id);
  //      $originalDate = Carbon::parse($model->start_at)->format('Y-m-d');
//
  //      $attendance->update([
    //        'user_id' =>$request->name->user_id,
      //      'start_at' =>$originalDate.$request->start,
        //    'finish_at' =>$originalDate.$request->finish,
//        ]);
  //      $rests->updateOrgreate([
    //        'attendance_id' =>$id,
      //      //'rest_start_at' =>$originalDate.$request->rests[{{ $index }}][id],
        //    'rest_finish_at' =>$originalDate.$request->finish,
        //]);


    //    return redirect('/');
    //}
}
