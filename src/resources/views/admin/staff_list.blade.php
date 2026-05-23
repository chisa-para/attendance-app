@extends('layouts.common')
@section('title' , 'スタッフ一覧ページ(管理者)')
@section('css')
<link rel="stylesheet" href="{{asset('css/staff.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="staff-list__content">
    <h1 class="staff-list__heading">スタッフ一覧</h1>
    <div class="staff-list-table">
        <table class="table">
            <tr class="table__row">
                <th class="table__head">名前</th>
                <th class="table__head">メールアドレス</th>
                <th class="table__head">月次勤怠</th>   
            </tr>
            <tr class="table__row">
                <td class="table__data">西怜奈</td>
                <td class="table__data">reina.n@coachtech.com</td>
                <td class="table__data--detail"><a href="">詳細</a></td>
            </tr>
        </table>
    </div>
</div>
@endsection