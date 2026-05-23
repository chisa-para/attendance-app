@extends('layouts.common')
@section('title' , 'ログインページ(管理者)')
@section('css')
<link rel="stylesheet" href="{{asset('css/auth.css')}}">
@endsection

@section('content')
@include('components.header')
<div class="auth-form__content">
    <div class="auth-form__heading">
        <h1>管理者ログイン</h1>
    </div>
    <form action="/login" method="post" class="auth__form">
        @csrf
        <label for="mail" class="entry__name">メールアドレス</label>
        <input name="email" id="mail" type="email" class="auth__input" value="{{ old('email') }}">
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
        <button class="auth__button">管理者ログインする</button>
    </form>
    
</div>
@endsection