@extends('layouts.common')
@section('title' , '日別勤怠一覧ページ(管理者)')
@section('css')
<link rel="stylesheet" href="{{asset('css/attendance.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="attendance-list__content">
    <h1 class="attendance-list__heading">勤怠一覧</h1>
    <div class="attendance-list-day">
        <a href="{{ route('admin.attendance.list', ['day' => $prevDate]) }}" class="last-day"><span class="arrow-left"></span>前日</a>
        <h2 class="month">{{ $displayDate }}</h2>
        <a href="{{ route('admin.attendance.list', ['day' => $nextDate]) }}" class="next-day">翌日<span class="arrow-right"></span></a>
    </div>
    <div class="attendance-list-table">
        <table class="table">
            <tr class="table__row">
                <th class="table__head">名前</th>
                <th class="table__head">出勤</th>
                <th class="table__head">退勤</th>  
                <th class="table__head">休憩</th>  
                <th class="table__head">合計</th>  
                <th class="table__head">詳細</th>  
            </tr>
            @foreach($attendances as $attendance)
            <tr class="table__row">
                <td class="table__data">{{ $attendance->user->name }}</td>
                <td class="table__data">{{ $attendance->start_at ? $attendance->start_at->format('H:i') : '-' }}</td>
                <td class="table__data">{{ $attendance->finish_at ? $attendance->finish_at->format('H:i') : '-' }}</td>
                <td class="table__data">{{ $attendance->display_total_rest_time }}</td>
                <td class="table__data">{{ $attendance->actual_work_time }}</td>
                <td class="table__data--detail"><a href="{{ route('admin.attendance.detail', ['attendance_id' => $attendance['id']]) }}">詳細</a></td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection