@extends('layouts.common')
@section('title' , '勤怠詳細ページ(一般)')
@section('css')
<link rel="stylesheet" href="{{asset('css/detail.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="attendance-detail__content">
    <h1 class="attendance-detail__heading">勤怠詳細</h1>
    <div class="attendance-detail-form">
        <form action="" class="form">
            @csrf
            <table class="table">
                <tr class="table__row">
                    <th class="table__head">名前</th>
                    <td class="table__data">
                        <input type="text" class="detail__input--fixed" name="name" value="西怜奈" readonly>
                    </td>
                </tr>
                <tr class="table__row">
                    <th class="table__head">日付</th>
                    <td class="table__data">
                        <input type="text" class="detail__input--fixed" name="date" value="2023年6月1日" readonly>
                    </td>
                </tr>
                <tr class="table__row">
                    <th class="table__head">出勤・退勤</th>
                    <td class="table__data">
                        <input type="text" class="detail__input" name="start_at" value="9:00">
                        <span>～</span>
                        <input type="text" class="detail__input" name="finish_at" value="18:00">
                    </td>
                </tr>
                <tr class="table__row">
                    <th class="table__head">休憩</th>
                    <td class="table__data">
                        <input type="text" class="detail__input" name="break_start_at" value="12:00">
                        <span>～</span>
                        <input type="text" class="detail__input" name="break_finish_at" value="13:00">
                    </td>
                </tr>
                <tr class="table__row">
                    <th class="table__head">休憩２</th>
                    <td class="table__data">
                        <input type="text" class="detail__input" name="break_start_at" value="">
                        <span>～</span>
                        <input type="text" class="detail__input" name="break_finish_at" value="">
                    </td>
                </tr>
                <tr class="table__row">
                    <th class="table__head">備考</th>
                    <td class="table__data">
                        <textarea type="text" class="detail__textarea" name="reason" cols="57" rows="10" placeholder="修正理由を記述してください" ></textarea>
                    </td>
                </tr>
            </table>
            <div class="form-button">
                <button class="approval-button">修正</button>
            </div>
        </form>
    </div>
</div>
@endsection