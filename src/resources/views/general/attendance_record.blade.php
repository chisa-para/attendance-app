@extends('layouts.common')
@section('title' , '勤怠記録ページ(一般)')
@section('css')
<link rel="stylesheet" href="{{asset('css/record.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="attendance-record__content">
    @if($status === 'before_work')
        <p class="attendance-status">勤務外</p>
    @elseif($status === 'working')
        <p class="attendance-status">出勤中</p>
    @elseif($status === 'resting')
        <p class="attendance-status">休憩中</p>
    @elseif($status === 'after_work')
        <p class="attendance-status">退勤済</p>
    @endif
    <p class="date">{{ $todayDate }}</p>
    <p class="time">{{ $nowTime }}</p>
    
    @if($status === 'before_work')
        <form action="{{ route('attendance.start') }}" method="post" class="attendance-form">
        @csrf
            <button class="attendance-button">出勤</button>
        </form>

    @elseif($status === 'working')
        <div class="attendance_button--working">
            <form action="{{ route('attendance.finish') }}" method="post" class="attendance-form">
            @csrf
                <button class="attendance-button">退勤</button>
            </form>
            <form action="{{ route('attendance.rest-start') }}" method="post" class="attendance-form">
            @csrf
                <button class="attendance-button__rest">休憩入</button>
            </form>
        </div>
    @elseif($status === 'resting')
        <form action="{{ route('attendance.rest-finish') }}" method="post" class="attendance-form">
        @csrf
            <button class="attendance-button__rest">休憩戻</button>
        </form>
    @elseif($status === 'after_work')
        <p>お疲れ様でした。</p>
    @endif
</div>
@endsection