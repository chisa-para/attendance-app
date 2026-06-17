<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ApprovalRequestController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminApprovalController;
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\GeneralOnly;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

//一般ミドルウェア
Route::middleware(['auth', GeneralOnly::class])->group(function (){
    Route::get('/attendance',[AttendanceController::class, 'home'])->name('attendance.date');
    Route::post('/attendance/start',[AttendanceController::class, 'start'])->name('attendance.start');
    Route::post('/attendance/finish',[AttendanceController::class, 'finish'])->name('attendance.finish');
    Route::post('/attendance/rest/start',[AttendanceController::class, 'rest'])->name('attendance.rest-start');
    Route::post('/attendance/rest/finish',[AttendanceController::class, 'restOut'])->name('attendance.rest-finish');

    Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.list');

    Route::get('/attendance/detail/{attendance_id}', [ApprovalRequestController::class, 'show'])->name('attendance.detail');

    Route::post('/attendance/detail/{attendance_id}', [ApprovalRequestController::class, 'store'])->name('attendance.change');
    
});

//管理者ログイン画面
Route::get('admin/login', function () {
    return app(\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class)->create(request());
})->name('login');

Route::post('admin/login', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'store']);

//管理者ミドルウェア
Route::middleware(['auth', AdminOnly::class])->group(function () {
    
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('attendance.list');

        Route::get('/attendance/{attendance_id}', [AdminAttendanceController::class, 'show'])->name('attendance.detail');

        Route::patch('/attendance/{attendance_id}', [AdminAttendanceController::class, 'update'])->name('attendance.update');

        Route::get('/staff/list', [AdminStaffController::class, 'index'])->name('staff.list');

        Route::get('/attendance/staff/{user_id}', [AdminAttendanceController::class, 'monthIndex'])->name('attendance.month');
    });

    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}',  [AdminApprovalController::class, 'show'])->name('approval.detail');

    Route::patch('/stamp_correction_request/approve/{attendance_correct_request_id}',  [AdminApprovalController::class, 'update'])->name('approval.update');
    });

//共通ミドルウェア
Route::middleware(['auth'])->group(function () {
    Route::get('/stamp_correction_request/list', [ApprovalRequestController::class, 'index'])
        ->name('request.list');
});

//メール認証ルート
// 1. 認証メール送信後の確認画面の表示
Route::get('/email/verify', function () {
    return view('general.auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 2. メール内のリンクをクリックした時の処理（検証完了）
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // これが認証完了の魔法のメソッドです
    return redirect('/attendance'); // 💡 認証後に飛ばしたい場所（マイページなど）に変更してください
})->middleware(['auth', 'signed'])->name('verification.verify');

// 3. 認証メールの再送処理
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return redirect()->route('verification.notice')->with('status', 'verification-link-sent');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//Route::get('/register', function () {
  //  return view('general.auth.register');
//});

//Route::get('/login', function () {
  //  return view('general.auth.login');
//});
