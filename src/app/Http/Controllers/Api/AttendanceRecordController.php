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
            // 🌟 users リレーション先のテーブルに対して条件を指定する
            return $query->whereHas('user', function ($subQuery) use ($userName) {
            // ※ 'name' の部分は、usersテーブルの実際の名前カラム名（name や user_name など）に合わせてください
            return $subQuery->where('name', 'like', '%' . $userName . '%');
            });
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
    public function show($id)
{
    // 1. 🌟 データベースからIDで検索し、なければ自分で404エラー（JSON）を即座に返す！
    // ※モデル名が Attendance の前提です。ご自身の環境に合わせてください。
    $attendanceRecord = Attendance::find($id);

    if (!$attendanceRecord) {
        return response()->json([
            'message' => '勤怠情報が見つかりませんでした。'
        ], 404);
    }

    // 2. 存在した場合は、関連テーブルを一気にロードして Resource で返却（あなたの元のコードです！）
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
            'comment'   => $request->reason,
        ]);

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
    public function update(UpdateAttendanceRecordRequest $request, $id)
{
    // 1. 🌟 対象の勤怠レコードを取得（なければ404）
    $attendanceRecord = Attendance::find($id);

    if (!$attendanceRecord) {
        return response()->json([
            'message' => '勤怠情報が見つかりませんでした。'
        ], 404);
    }

    // 2. 🌟 要件の指示：Policyを呼び出して、本人かどうかの「認可」チェック（ダメなら自動で403）
    //$this->authorize('update', $attendanceRecord);
    Gate::authorize('update', $attendanceRecord);

    // 3. バリデーション済みの安全なデータを取得
    $validated = $request->validated();

    // 4. 🌟 送られてきた日付と時刻を結合して日時にする
    $startDateTime = $request->date . ' ' . $request->clock_in;
    $finishDateTime = null;
    if ($request->clock_out) {
        $finishDateTime = $request->date . ' ' . $request->clock_out;
    }

    // 5. 🌟 要件の指示：レコードを更新する
    $attendanceRecord->update([
        'start_at'  => $startDateTime,
        'finish_at' => $finishDateTime,
        'retouch_reason'   => $request->comment, // データベース側のカラム名に合わせて指定してください
    ]);

    // 6. 🌟 要件の指示：更新後に関連データをロードして Resource を返す
    $attendanceRecord->load(['user', 'rests']); // ※要件の「breaks」は実環境の「rests」に合わせます

    return new AttendanceRecordResource($attendanceRecord);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    // 1. 🌟 対象の勤怠レコードを取得（なければ404）
    $attendanceRecord = Attendance::find($id);

    if (!$attendanceRecord) {
        return response()->json([
            'error' => '勤怠情報が見つかりませんでした。' // 🔑 要件指定のキー名「error」
        ], 404);
    }

    // 2. 🌟 要件の指示：Policyを呼び出して「認可」チェック
    // 認可失敗時に要件通りのメッセージ（error）と403を返したいため、例外をキャッチするか Gate::allows を使います
    if (Gate::denies('delete', $attendanceRecord)) {
        return response()->json([
            'error' => 'この操作を実行する権限がありません。' // 🔑 要件指定のメッセージ
        ], 403);
    }

    // 3. 🌟 要件の指示：レコードを削除する
    // データベース側で ON DELETE CASCADE が設定されているため、紐づく関連データも自動で連動削除されます
    $attendanceRecord->delete();

    // 4. 🌟 成功時は 204 No Content（ボディなし）を返す
    return response()->noContent();
}
}
