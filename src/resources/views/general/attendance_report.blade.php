@extends('layouts.common')
@section('title' , '勤怠レポートページ(一般)')
@section('css')
<link rel="stylesheet" href="{{asset('css/report.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="attendance-report__content">
    <h1 class="attendance-report__heading">マイ勤怠レポート</h1>
    <p class="attendance-report__explain">過去6か月のデータから集計しています。</p>
    <h2 class="attendance-report__second-heading">基本サマリー</h2>
    <div class="attendance-report-display">
        <div class="result-display">
            <h3 class="result-heading">総労働時間</h3>
            <p class="result-content">{{ $summary6Months['total_work_time_str'] }}</p>
        </div>
        <div class="result-display">
            <h3 class="result-heading">総残業時間</h3>
            <p class="result-content">{{ $summary6Months['total_overtime_time_str'] }}</p>
        </div>
        <div class="result-display">
            <h3 class="result-heading">平均労働時間/日</h3>
            <p class="result-content">{{ $summary6Months['average_work_time_str'] }}</p>
        </div>
    </div>
    <h2 class="attendance-report__second-heading">月次遷移(過去6ヶ月)</h2>
    <table class="table">
        <tr class="table__row">
            <th class="table__head--month">月</th>
            <th class="table__head">労働時間</th>
            <th class="table__head">残業時間</th>  
        </tr>
        @foreach($monthlyAttendance as $month => $data)
        <tr class="table__row">
            <td class="table__data">{{ $month }}</td>
            <td class="table__data">{{ $data['total_time_str'] }}</td>
            <td class="table__data">{{ $data['overtime_time_str'] }}</td>
        </tr>
        @endforeach
    </table>
    <h2 class="attendance-report__second-heading">今月の異常検知</h2>
    <p class="attendance-report__supplement">基準：始業09:00/終業18:00/長時間労働1日10時間超</p>
    <div class="attendance-report-display">
        <div class="result-display">
            <h3 class="result-heading">遅刻回数</h3>
            <p class="result-content">{{ $data['late_count'] }}回</p>
        </div>
        <div class="result-display">
            <h3 class="result-heading">早退回数</h3>
            <p class="result-content">{{ $data['early_leave_count'] }}回</p>
        </div>
        <div class="result-display">
            <h3 class="result-heading">長時間労働日数</h3>
            <p class="result-content">{{ $data['long_work_count'] }}日</p>
        </div>
    </div>
</div>
@endsection