@extends('layouts.common')
@section('title' , '申請一覧ページ(一般)')
@section('css')
<link rel="stylesheet" href="{{asset('css/approval-request.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="approval-request__content">
    <h1 class="approval-request__heading">申請一覧</h1>
    <div class="status-navi">
        <ul class="approval-request__navi">
            <li class="navi-item"><a href="">承認待ち</a></li>
            <li class="navi-item"><a href="">承認済み</a></li>
        </ul>
    </div>
    <div class="approval-request-table">
        <table class="table">
            <tr class="table__row">
                <th class="table__head">状態</th>
                <th class="table__head">名前</th>
                <th class="table__head">対象日時</th>  
                <th class="table__head">申請理由</th>  
                <th class="table__head">申請日時</th>  
                <th class="table__head">詳細</th>  
            </tr>
            <tr class="table__row">
                <td class="table__data">承認待ち</td>
                <td class="table__data">西怜奈</td>
                <td class="table__data">2023/06/01</td>
                <td class="table__data">電車が遅延したため</td>
                <td class="table__data">2023/06/02</td>
                <td class="table__data--detail"><a href="">詳細</a></td>
            </tr>
        </table>
    </div>
</div>
@endsection