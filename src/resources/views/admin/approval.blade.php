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
        <form action="{{ route('approval.update', ['attendance_correct_request_id' => $id]) }}" class="form" method="post">
            @csrf
            @method('PATCH')
            <table class="table">
                <tr class="table__row">
                    <th class="table__head">名前</th>
                    <td class="table__data">{{ $approvalRequest->attendance->user->name }}</td>
                    
                </tr>
                <tr class="table__row">
                    <th class="table__head">日付</th>
                    <td class="table__data">{{ $date }}</td>
                </tr>
                <tr class="table__row">
                    <th class="table__head">出勤・退勤</th>
                    <td class="table__data">
                        {{ $start }}
                        <input type="hidden" class="detail__input--fixed" name="start_at" value="{{ $start }}" readonly>
                        <span>～</span>
                        {{ $finish }}
                        <input type="hidden" class="detail__input--fixed" name="finish_at" value="{{ $finish }}" readonly>
                    </td>
                    
                </tr>
                @foreach($rests as $index => $rest)
                <tr class="table__row">
                    <th class="table__head">休憩{{ $index + 1 }}</th>

                    @if($rest->id)
                    <input type="hidden" name="rests[{{ $index }}][id]" value="{{ $rest->id }}">
                    @endif

                    <td class="table__data">
                        {{ $rest->request_rest_start_at ? \Carbon\Carbon::parse($rest->request_rest_start_at)->format('H:i') : '' }}
                        <input type="hidden" class="detail__input" name="request_rest_start_at" value="{{ $rest->request_rest_start_at ? \Carbon\Carbon::parse($rest->request_rest_start_at)->format('H:i') : '' }}">
                        <span>～</span>
                        {{ $rest->request_rest_finish_at ? \Carbon\Carbon::parse($rest->request_rest_finish_at)->format('H:i') : '' }}
                        <input type="hidden" class="detail__input" name="request_rest_finish_at" value="{{ $rest->request_rest_finish_at ? \Carbon\Carbon::parse($rest->request_rest_finish_at)->format('H:i') : '' }}">
                    </td>
                </tr>
                @endforeach
                <tr class="table__row">
                    <th class="table__head">備考</th>
                    <td class="table__data">
                        {{ $approvalRequest->reason }}
                        <input type="hidden" class="detail__input--fixed" name="finish_at" value="{{ $approvalRequest->reason }}" readonly>
                    </td>
                </tr>
            </table>
            <div class="form-button">
                @if($approvalRequest->status === 'waiting')
                    <button class="approval-button">承認</button>
                @else
                    <div class="approval-button__approved" style="color: red; font-weight: bold; margin-top: 15px;">
                    承認済み
                </div>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection