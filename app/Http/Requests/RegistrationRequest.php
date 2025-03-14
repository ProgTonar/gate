<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'last_name' => 'required|string|min:1|max:255',
            'first_name' => 'required|string|min:1|max:255',
            'middle_name' => 'nullable|string|min:1|max:255',
            'login' => 'required|string|min:1|max:255|unique:users,login',
            'email' => 'nullable|string|min:1|email|max:255|unique:users,email',
            'password' => ['required', Password::defaults()],
        ];
    }

    public function messages()
    {
        return [
            'last_name.required' => 'Фамилия обязательна для заполнения',
            'last_name.string' => 'Фамилия должна быть строкой',
            'last_name.min' => 'Фамилия должна содержать хотя бы 1 символ',
            'last_name.max' => 'Фамилия не может превышать 255 символов',

            'first_name.required' => 'Имя обязательно для заполнения',
            'first_name.string' => 'Имя должно быть строкой',
            'first_name.min' => 'Имя должно содержать хотя бы 1 символ',
            'first_name.max' => 'Имя не может превышать 255 символов',

            'middle_name.string' => 'Отчество должно быть строкой',
            'middle_name.min' => 'Отчество должно содержать хотя бы 1 символ',
            'middle_name.max' => 'Отчество не может превышать 255 символов',

            'login.required' => 'Логин обязателен для заполнения',
            'login.string' => 'Логин должен быть строкой',
            'login.min' => 'Логин должен содержать хотя бы 1 символ',
            'login.max' => 'Логин не может превышать 255 символов',
            'login.unique' => 'Такой логин уже используется',

            'email.email' => 'Неверный формат email',
            'email.string' => 'Email должен быть строкой',
            'email.min' => 'Email должен содержать хотя бы 1 символ',
            'email.max' => 'Email не может превышать 255 символов',
            'email.unique' => 'Такой email уже зарегистрирован',

            'password.required' => 'Пароль обязателен для заполнения',
            'password.string' => 'Пароль должен быть строкой',
            'password.min' => 'Пароль должен содержать минимум :min символов',
            'password.confirmed' => 'Пароли не совпадают',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }
}
