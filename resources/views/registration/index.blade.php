@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
<div class="container">
    <input class="inputic" id="last_name" type="text" placeholder="Фамилия">
    <input class="inputic" id="first_name" type="text" placeholder="Имя">
    <input class="inputic" id="middle_name" type="text" placeholder="Отчество">
    <input class="inputic" id="email" type="email" placeholder="почта">
    <input class="inputic" id="login" type="text" placeholder="логин">
    <input class="inputic" id="password" type="password" placeholder="пароль">
    <button class="subBtn" id="submitBtn">Регистрация</button>
    <a class="linkReg" href="{{ route('loginPage') }}">Авторизация</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let lastNameInput = document.getElementById('last_name');
    let firstNameInput = document.getElementById('first_name');
    let middleNameInput = document.getElementById('middle_name');
    let emailInput = document.getElementById('email');
    let loginInput = document.getElementById('login');
    let passwordInput = document.getElementById('password');
    let submitButton = document.getElementById('submitBtn');

    submitButton.addEventListener('click', function (event) {
        event.preventDefault();

        // Получаем значения из полей ввода
        let lastName = lastNameInput.value.trim();
        let firstName = firstNameInput.value.trim();
        let middleName = middleNameInput.value.trim();
        let email = emailInput.value.trim();
        let login = loginInput.value.trim();
        let password = passwordInput.value.trim();

        // Проверяем, что все обязательные поля заполнены
        if (!login || !password || !email) {
            alert('Пожалуйста, заполните все обязательные поля!');
            return;
        }

        // URL для отправки данных
        let url = "{{ route('registration') }}"; // Обратите внимание на кавычки

        // Создаем объект с данными
        let data = {
            last_name: lastName,
            first_name: firstName,
            middle_name: middleName,
            email: email,
            login: login,
            password: password,
        };

        // Отправляем данные через AJAX
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            // Проверяем статус ответа
            if (!response.ok) {
                throw new Error('Ошибка при отправке данных. Код статуса: ' + response.status);
            }

            return response.json();
        })
        .then(result => {
            console.log('Ответ сервера (распарсенный):', result);

            if (result && result.message) {
                alert(result.message);
            } else {
                alert('Не удалось получить сообщение от сервера.');
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при регистрации.');
        });
    });
});
</script>

@endsection
