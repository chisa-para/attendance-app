<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceRecordController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Route::get('/test', function () {
    //return response()->json(['message' => 'Hello API!']);
//});

Route::apiResource('v1/attendance', AttendanceRecordController::class);

Route::prefix('v1')->group(function () {
    Route::get('attendance-records', [AttendanceRecordController::class, 'index']);
    Route::get('attendance-records/{attendanceRecord}', [AttendanceRecordController::class, 'show']);

    // トークン（Sanctum）必要
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('attendance-records', [AttendanceRecordController::class, 'store']);
        Route::put('attendance-records/{id}', [AttendanceRecordController::class, 'update']);
        Route::delete('attendance-records/{id}', [AttendanceRecordController::class, 'destroy']);
    });
    
});

Route::post('v1/login', function (Request $request) {
    //本来はパスワードチェックをしますが、テスト用にメールアドレスだけでトークンを発行しちゃいます
    $user = \App\Models\User::where('email', $request->email)->first();
    
    if (!$user) {
        return response()->json(['message' => 'ユーザーが見つかりません'], 401);
    }

    //トークンを発行する（Sanctumの機能）
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
    ]);
});