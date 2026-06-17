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
        <form action="{{ route('attendance.change', ['attendance_id' => $attendance['id']]) }}" class="form" method="post">
            @csrf
            
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

            <table class="table">
                <tr class="table__row">
                    <th class="table__head">名前</th>
                    <td class="table__data">
                        <input type="text" class="detail__input--fixed" name="name" value="{{ $user->name }}" readonly>
                    </td>
                </tr>
                <tr class="table__row">
                    <th class="table__head">日付</th>
                    <td class="table__data">
                        <input type="text" class="detail__input--fixed" name="date" value="{{ $date }}" readonly>
                    </td>
                </tr>
                <tr class="table__row">
                    <th class="table__head">出勤・退勤</th>
                    <td class="table__data">
                        <input type="time" class="detail__input" name="request_start_at" value="{{ old('request_start_at', $start) }}">
                        <span>～</span>
                        <input type="time" class="detail__input" name="request_finish_at" value="{{ old('request_finish_at', $finish) }}">
                        @if ($errors->has('request_start_at') || $errors->has('request_finish_at'))
                        <div class="form__error" style="color: red; padding: 10px;">
                            {{ $errors->first('request_start_at') ?? $errors->first('request_finish_at') }}
                        </div>
                        @endif
                    </td>
                </tr>
                @foreach($rests as $index => $rest)
                <tr class="table__row">
                    <th class="table__head">休憩{{ $index + 1 }}</th>

                    @if($rest->id)
                    <input type="hidden" name="rests[{{ $index }}][id]" value="{{ $rest->id }}">
                    @endif

                    <td class="table__data">
                        <input type="time" class="detail__input" name="rests[{{ $index }}][request_rest_start_at]" value="{{ old("rests.{$index}.request_rest_start_at",$rest->rest_start_at ? \Carbon\Carbon::parse($rest->rest_start_at)->format('H:i') : '') }}">
                        <span>～</span>
                        <input type="time" class="detail__input" name="rests[{{ $index }}][request_rest_finish_at]" value="{{ old("rests.{$index}.request_rest_finish_at",$rest->rest_finish_at ? \Carbon\Carbon::parse($rest->rest_finish_at)->format('H:i') : '') }}">

                        @if ($errors->has("rests.{$index}.request_rest_start_at") || $errors->has("rests.{$index}.request_rest_finish_at"))
                        <div class="form__error" style="color: red; padding: 10px;">
                            {{ $errors->first("rests.{$index}.request_rest_start_at") ?? $errors->first("rests.{$index}.request_rest_finish_at") }}
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
                <tr class="table__row">
                    <th class="table__head">備考</th>
                    <td class="table__data">
                        @if(!$retouch_reason)
                        <textarea type="text" class="detail__textarea" name="reason" cols="57" rows="10" placeholder="修正理由を記述してください" >{{ old('reason') }}</textarea>
                        <div class="form__error" style="color: red; padding: 10px;">
                            @error('reason')
                            {{ $message }}
                            @enderror
                        </div>
                        @else
                        <textarea type="text" class="detail__textarea" name="reason" cols="57" rows="10" placeholder="修正理由を記述してください" >{{ old('reason') }}</textarea>
                        @endif
                    </td>
                </tr>
            </table>
            <div class="form-button">
                @if($isPending)
                <div class="alert alert--warning" style="color: red; font-weight: bold; margin-top: 15px;">
                    *申請中のため修正できません
                </div>
                @else
                <button class="approval-button">修正</button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection