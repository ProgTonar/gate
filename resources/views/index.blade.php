@extends('layouts.app')

@section('title', 'Авторизация')

@section('content')
    <div class="container">
        <input class="inputic" id="login" type="text" placeholder="Логин">
        <input class="inputic" id="password" type="password" placeholder="Пароль">
        <button class="subBtn" id="submitBtn">Войти</button>
        <a class="linkReg" href="{{ route('registrationPage') }}">Регистрация</a>
    </div>
@endsection
