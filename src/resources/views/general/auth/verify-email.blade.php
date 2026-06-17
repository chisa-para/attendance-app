@extends('layouts.common')
@section('title' , '新規登録メール認証ページ(一般)')
@section('css')
<link rel="stylesheet" href="{{asset('css/auth.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="mail__content">
    <div>
        @if (session('status') == 'verification-link-sent')
        <div class="email__resend">
            新しい認証リンクをメールアドレスに送信しました！
        </div>
        @endif
    </div>
    <div class="mail__text">
        <h3 class="mail-guidance1">登録していただいたメールアドレスに認証メールを送付しました。</h3>
        <h3 class="mail-guidance2">メール認証を完了してください。</h3>
        <a class="mail__verify-button" href="http://localhost:8025/#" target="_blank" rel="noopener noreferrer">認証はこちらから</a>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="email__resend-button"  type="submit">認証メールを再送する</button>
        </form>
    </div>
</div>
@endsection