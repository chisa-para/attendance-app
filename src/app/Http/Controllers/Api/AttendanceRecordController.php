<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Http\Requests\Api\V1\IndexAttendanceRecordRequest;
use App\Http\Requests\Api\V1\StoreAttendanceRecordRequest;
use App\Http\Resources\Api\V1\AttendanceRecordResource;

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
        ->when($request->date, function ($query, $date) {
            return $query->whereDate('start_at', $date);
        })
        ->when($request->month, function ($query, $month) {
            return $query->where('start_at', 'like', $month . '%');
        })
        // dateカラムの新しい順にしてページネーション
        ->latest('start_at') 
        ->paginate($perPage);

       // Resourceコレクションを使った return
       return AttendanceRecordResource::collection($attendanceRecords);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function show(Attendance $attendanceRecord)
    {
        $attendanceRecord->load(['user', 'rests', 'approvalRequests']);
        
        return new AttendanceRecordResource($attendanceRecord);
    }

    /**
     * Display the specified resource.
     */
    public function store(StoreAttendanceRecordRequest $request)
    {
        $validated = $request->validated();

        // 2. 🌟 認証ユーザーから user_id を自動付与して勤怠データを作成
        // ※リレーション名が attendances の場合は書き換えてください
        $attendanceRecord = $request->user()->attendances()->create($validated);

        // 3. 🌟 作成後に関連データを eager load
        $attendanceRecord->load(['user', 'rests']);

        // 4. 🌟 Resourceでラップし、ステータスコード 201（作成成功）を明示して返す
        return (new AttendanceRecordResource($attendanceRecord))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Gate::authorize('update', $attendanceRecord);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('delete', $attendanceRecord);
    }
}
