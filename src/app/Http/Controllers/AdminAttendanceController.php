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
use App\Http\Requests\AdminAtetndanceRequest;

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

        $attendances = Attendance::whereDate('start_at', $todayDate)->with('user','rests')->get();

        return view('admin.attendance_list',compact('displayDate', 'attendances','prevDate','nextDate'));
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
        $attendance = Attendance::where('id',$id)->with('user','rests');
        $rests = $attendance->rests;
        
        $originalDate = Carbon::parse($model->start_at)->format('Y-m-d');

        $attendance->update([
            'user_id' =>$request->name->user_id,
            'start_at' =>$originalDate.$request->start,
            'finish_at' =>$originalDate.$request->finish,
        ]);
        $rests->updateOrgreate([
            'attendance_id' =>$id,
            'rest_start_at' =>$originalDate.$request->rests[{{ $index }}][id],
            'rest_finish_at' =>$originalDate.$request->finish,
        ]);


        return redirect('/');
    }

}
