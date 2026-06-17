@extends('layouts.common')
@section('title' , '勤怠一覧ページ(一般)')
@section('css')
<link rel="stylesheet" href="{{asset('css/attendance.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="attendance-list__content">
    <h1 class="attendance-list__heading">勤怠一覧</h1>
    <div class="attendance-list-month">
        <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="last-month"><span class="arrow-left"></span>前月</a>
        <h2 class="month">{{ $targetMonth->format('Y年m月') }}</h2>
        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="next-month">翌月<span class="arrow-right"></span></a>
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
                        <a href="{{ route('attendance.detail', ['attendance_id' => $attendance['id'],'from' => 'attendance']) }}">詳細</a>
                    @else
                        <p>詳細</p>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection