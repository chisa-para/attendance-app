<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.common');
});

Route::get('/register', function () {
    return view('general.auth.register');
});

Route::get('/login', function () {
    return view('general.auth.login');
});

Route::get('/attendance', function () {
    return view('general.attendance_record');
});

Route::get('/attendance/list', function () {
    return view('general.attendance_list');
});

Route::get('/attendance/detail', function () {
    return view('general.attendance_detail');
});

Route::get('/stamp_correction_request/list', function () {
    return view('general.approval_request_list');
});

Route::get('/admin/login', function () {
    return view('admin.auth.login');
});

Route::get('/admin/attendance/list', function () {
    return view('admin.attendance_list');
});

Route::get('/admin/attendance', function () {
    return view('admin.attendance_detail');
});

Route::get('/admin/staff/list', function () {
    return view('admin.staff_list');
});

Route::get('/admin/attendance/staff', function () {
    return view('admin.each_attendance_list');
});

//Route::get('/stamp_correction_request/list', function () {
//    return view('admin.attendance_detail');
//});

Route::get('/stamp_correction_request/approve', function () {
    return view('admin.approval');
});
