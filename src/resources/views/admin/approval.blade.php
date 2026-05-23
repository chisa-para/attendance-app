@extends('layouts.common')
@section('title' , '申請承認ページ(管理者)')
@section('css')
<link rel="stylesheet" href="{{asset('css/approval.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="approval__content">
    <h1 class="approval__heading">勤怠詳細</h1>
    <div class="approval-form">
        <form action="" class="form">
            @csrf
            <table class="table">
                <tr class="table__row">
                    <th class="table__head">名前</th>
                    <td class="table__data">西怜奈</td>
                    
                </tr>
                <tr class="table__row">
                    <th class="table__head">日付</th>
                    <td class="table__data">2023年6月1日</td>
                    
                </tr>
                <tr class="table__row">
                    <th class="table__head">出勤・退勤</th>
                    <td class="table__data">9:00<span>～</span>18:00</td>
                    
                </tr>
                <tr class="table__row">
                    <th class="table__head">休憩</th>
                    <td class="table__data">12:00<span>～</span>13:00</td>
                    
                </tr>
                <tr class="table__row">
                    <th class="table__head">休憩２</th>
                    <td class="table__data"></td>
                </tr>
                <tr class="table__row">
                    <th class="table__head">備考</th>
                    <td class="table__data">hiddenのinputをつけるのを忘れずに</td>
                </tr>
            </table>
            <div class="form-button">
                <button class="approval-button">承認</button>
            </div>
        </form>
    </div>
</div>
@endsection