@extends('layouts.common')
@section('title' , '新規登録ページ(一般)')
@section('css')
<link rel="stylesheet" href="{{asset('css/auth.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="auth-form__content">
    <div class="auth-form__heading">
        <h1>会員登録</h1>
    </div>
    <form action="/register" method="post" class="auth__form" novalidate>
        @csrf
        <label for="name" class="entry__name">ユーザ名</label>
        <input name="name" id="name" type="text" class="auth__input" value="{{ old('name') }}">
        <div class="form__error">
            @error('name')
            {{ $message }}
            @enderror
        </div>
        <label for="email" class="entry__name">メールアドレス</label>
        <input name="email" id="email" type="email" class="auth__input" value="{{ old('email') }}">
        <div class="form__error">
            @error('email')
            {{ $message }}
            @enderror
        </div>
        <label for="password" class="entry__name">パスワード</label>
        <input name="password" id="password" type="password" class="auth__input">
        <div class="form__error">
            @error('password')
            {{ $message }}
            @enderror
        </div>
        <label for="password_confirm" class="entry__name">確認用パスワード</label>
        <input name="password_confirmation" id="password_confirm" type="password" class="auth__input">
        <button class="auth__button">登録する</button>
    </form>

    <a href="/login" class="link">ログインはこちら</a>

</div>
@endsection