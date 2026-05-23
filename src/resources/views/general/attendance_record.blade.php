@extends('layouts.common')
@section('title' , '勤怠記録ページ(一般)')
@section('css')
<link rel="stylesheet" href="{{asset('css/record.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="attendance-record__content">
    <p class="attendance-status">勤務外</p>
    <p class="date">2023年6月1日(木)</p>
    <p class="time">08:00</p>
    <form action="" class="attendance-form">
        <button class="attendance-button">出勤</button>
    </form>
</div>
@endsection