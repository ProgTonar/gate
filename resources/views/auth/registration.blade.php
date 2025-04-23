<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .registration-container {
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #4CAF50;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <h1>Регистрация</h1>
        
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif
        
        @if($errors->any())
            <div class="error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('registration') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="last_name">Фамилия:</label>
                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
            </div>
            <div class="form-group">
                <label for="first_name">Имя:</label>
                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
            </div>
            <div class="form-group">
                <label for="middle_name">Отчество:</label>
                <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label for="login">Логин:</label>
                <input type="text" id="login" name="login" value="{{ old('login') }}" required>
            </div>
            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}">
            </div>
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Подтверждение пароля:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
            <input type="hidden" name="redirect_url" value="{{ $redirectUrl ?? '' }}">
            <button type="submit">Зарегистрироваться</button>
        </form>
        
        <div class="login-link">
            <p>Уже есть аккаунт? <a href="{{ route('loginPage', ['redirect_url' => $redirectUrl ?? '']) }}">Войти</a></p>
        </div>
    </div>
</body>
</html> 