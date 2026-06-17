@extends('layouts.common')
@section('title' , 'スタッフ別勤怠一覧ページ(管理者)')
@section('css')
<link rel="stylesheet" href="{{asset('css/attendance.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="attendance-list__content">
    <h1 class="attendance-list__heading">{{ $user->name }}さんの勤怠</h1>
    <div class="attendance-list-month">
        <a href="{{ route('admin.attendance.month', ['user_id' => $user->id, 'month' => $prevMonth]) }}" class="last-month"><span class="arrow-left"></span>前月</a>
        <h2 class="month">{{ $targetMonth->format('Y年m月') }}</h2>
        <a href="{{ route('admin.attendance.month', ['user_id' => $user->id, 'month' => $nextMonth]) }}" class="next-month">翌月<span class="arrow-right"></span></a>
    </div>
    <div class="attendance-list-table">
        <table class="table">
            <tr class="table__row">
                <th class="table__head">日付</th>
                <th class="table__head">出勤</th>
                <th class="table__head">退勤</th>  
                <th class="table__head">休憩</th>  
                <th class="table__head">合計</th>  
                <th class="table__head">詳細</th>  
            </tr>
            @foreach($attendanceList as $attendance)
            <tr class="table__row">
                <td class="table__data">{{ $attendance['date'] }}</td>
                <td class="table__data">{{ $attendance['start_at'] }}</td>
                <td class="table__data">{{ $attendance['finish_at'] }}</td>
                <td class="table__data">{{ $attendance['rest_time'] }}</td>
                <td class="table__data">{{ $attendance['working_time'] }}</td>
                <td class="table__data--detail">
                    @if(!empty($attendance['id']))
                        <a href="{{ route('admin.attendance.detail', ['attendance_id' => $attendance['id']]) }}">詳細</a>
                    @else
                        <p>詳細</p>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    <form action="/admin/export" class="export-form">
        @csrf
        @foreach(request()->query() as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <button class="export-button">CSV出力</button>
    </form>
</div>
@endsection