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
        <a href="" class="last-day"><span class="arrow-left"></span>前日</a>
        <h2 class="month">2023/06/01</h2>
        <a href="" class="next-day">翌日<span class="arrow-right"></span></a>
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
            <tr class="table__row">
                <td class="table__data">西怜奈</td>
                <td class="table__data">9:00</td>
                <td class="table__data">18:00</td>
                <td class="table__data">1:00</td>
                <td class="table__data">8:00</td>
                <td class="table__data--detail"><a href="">詳細</a></td>
            </tr>
        </table>
    </div>
</div>
@endsection