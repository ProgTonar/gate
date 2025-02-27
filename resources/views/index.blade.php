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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let loginInput = document.getElementById('login');
        let passwordInput = document.getElementById('password');
        let submitButton = document.getElementById('submitBtn');

        submitButton.addEventListener('click', function (event) {
            event.preventDefault();

            let login = loginInput.value.trim();
            let password = passwordInput.value.trim();

            let url = "{{ route('authLogin') }}";

            if (!login || !password) {
                alert('Пожалуйста, заполните все поля!');
                return;
            }

            let data = {
                login: login,
                password: password
            };

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка при отправке данных');
                }
                return response.json();
                console.log('Ответ сервера:', result);
            })
            .then(result => {
                console.log('Ответ сервера:', result);
                window.location.href = result.link;
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при отправке данных.');
            });
        });
    });
</script>
