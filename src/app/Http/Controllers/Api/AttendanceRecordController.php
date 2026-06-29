<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Http\Requests\Api\V1\IndexAttendanceRecordRequest;
use App\Http\Requests\Api\V1\StoreAttendanceRecordRequest;
use App\Http\Requests\Api\V1\UpdateAttendanceRecordRequest;
use App\Http\Resources\Api\V1\AttendanceRecordResource;
use Illuminate\Support\Facades\Gate;

class AttendanceRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexAttendanceRecordRequest $request)
    {
        $perPage = $request->input('per_page', 20);

        $attendanceRecords = Attendance::with(['user', 'rests']) 
        ->when($request->user_id, function ($query, $userId) {
            return $query->where('user_id', $userId);
        })
        ->when($request->user_name, function ($query, $userName) {
            // users リレーション先のテーブルに対して条件を指定する
            return $query->whereHas('user', function ($subQuery) use ($userName) {
                return $subQuery->where('name', 'like', '%' . $userName . '%');
            });
        })
        ->when($request->date, function ($query, $date) {
            return $query->whereDate('start_at', $date);
        })
        ->when($request->month, function ($query, $month) {
            return $query->where('start_at', 'like', $month . '%');
        })
        ->latest('start_at') 
        ->paginate($perPage);

       return AttendanceRecordResource::collection($attendanceRecords);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function show($id)
    {
        $attendanceRecord = Attendance::find($id);

        if (!$attendanceRecord) {
            return response()->json([
                'message' => '勤怠情報が見つかりませんでした。'
            ], 404);
        }
        
        $attendanceRecord->load(['user', 'rests', 'approvalRequests']);
        return new AttendanceRecordResource($attendanceRecord);
    }

    /**
     * Display the specified resource.
     */
    public function store(StoreAttendanceRecordRequest $request)
    {
        if (!$request->user()) {
            return response()->json([
                'message' => '認証されていません。再ログインしてください。'
            ], 401);
        }

        $startDateTime = $request->date . ' ' . $request->clock_in;

        $finishDateTime = null;
        if ($request->clock_out) {
            $finishDateTime = $request->date . ' ' . $request->clock_out;
        }

        $attendanceRecord = $request->user()->attendances()->create([
            'start_at'  => $startDateTime,
            'finish_at' => $finishDateTime,
            'retouch_reason'   => $request->comment,
        ]);

        $attendanceRecord->load(['user', 'rests']);

        return (new AttendanceRecordResource($attendanceRecord))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRecordRequest $request, $id)
    {
        $attendanceRecord = Attendance::find($id);
        
        if (!$attendanceRecord) {
            return response()->json([
                'message' => '勤怠情報が見つかりませんでした。'
            ], 404);
        }

        // 「認可」チェック
        //$this->authorize('update', $attendanceRecord);
        Gate::authorize('update', $attendanceRecord);

        $validated = $request->validated();

        // 送られてきた日付と時刻を結合して日時にする
        $startDateTime = $request->date . ' ' . $request->clock_in;
        $finishDateTime = null;
        if ($request->clock_out) {
            $finishDateTime = $request->date . ' ' . $request->clock_out;
        }

        // レコードを更新する
        $attendanceRecord->update([
            'start_at'  => $startDateTime,
            'finish_at' => $finishDateTime,
            'retouch_reason'   => $request->comment,
        ]);

    
        $attendanceRecord->load(['user', 'rests']);

        return new AttendanceRecordResource($attendanceRecord);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $attendanceRecord = Attendance::find($id);

        if (!$attendanceRecord) {
            return response()->json([
                'error' => '勤怠情報が見つかりませんでした。' // 🔑 要件指定のキー名「error」
            ], 404);
        }

        // Policyを呼び出して「認可」チェック
        if (Gate::denies('delete', $attendanceRecord)) {
            return response()->json([
                'error' => 'この操作を実行する権限がありません。' // 🔑 要件指定のメッセージ
            ], 403);
        }

        $attendanceRecord->delete();

        // 成功時は 204 No Content（ボディなし）を返す
        return response()->noContent();
    }
}
