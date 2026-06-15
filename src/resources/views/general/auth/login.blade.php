@extends('layouts.common')
@section('title' , 'ログインページ(一般)')
@section('css')
<link rel="stylesheet" href="{{asset('css/auth.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="auth-form__content">
    <div class="auth-form__heading">
        <h1>ログイン</h1>
    </div>
    <form action="/login" method="post" class="auth__form" novalidate>
        @csrf
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
        <button class="auth__button">ログインする</button>
    </form>

    <a href="/register" class="link">会員登録はこちら</a>
    
</div>
@endsection