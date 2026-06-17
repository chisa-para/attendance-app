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
            <li class="navi-item"><a href="{{ route('request.list', ['status' => 'waiting', 'keyword' => $keyword]) }}">承認待ち</a></li>
            <li class="navi-item"><a href="{{ route('request.list', ['status' => 'approved', 'keyword' => $keyword]) }}">承認済み</a></li>
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
            @foreach($requests as $requestItem)
            <tr class="table__row">
                <td class="table__data">
                    @if($requestItem->status === 'waiting')
                        <span class="status-badge--waiting">承認待ち</span>
                    @elseif($requestItem->status === 'approved')
                        <span class="status-badge--approved">承認済み</span>
                    @else
                        <span class="status-badge--rejected">否認</span>
                    @endif
                </td>
                <td class="table__data">{{ $requestItem->attendance->user->name ?? '不明' }}</td>
                <td class="table__data">{{ \Carbon\Carbon::parse($requestItem->attendance->start_at)->format('Y/m/d') }}</td>
                <td class="table__data">{{ $requestItem->reason }}</td>
                <td class="table__data">{{ $requestItem->created_at->format('Y/m/d') }}</td>
                <td class="table__data--detail">
                    @if(Auth::user()->admin_status)
                        <a href="{{ route('approval.detail', ['attendance_correct_request_id' => $requestItem->id]) }}">詳細</a>
                    @else
                        <a href="{{ route('attendance.detail', ['attendance_id' => $requestItem->attendance_id,'from' => 'approval']) }}">詳細</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection