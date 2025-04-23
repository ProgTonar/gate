<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegistrationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'login' => 'required|string|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'last_name.required' => 'Фамилия обязательна для заполнения',
            'last_name.string' => 'Фамилия должна быть строкой',
            'last_name.max' => 'Фамилия не может превышать 255 символов',
            
            'first_name.required' => 'Имя обязательно для заполнения',
            'first_name.string' => 'Имя должно быть строкой',
            'first_name.max' => 'Имя не может превышать 255 символов',
            
            'middle_name.string' => 'Отчество должно быть строкой',
            'middle_name.max' => 'Отчество не может превышать 255 символов',
            
            'email.string' => 'Email должен быть строкой',
            'email.email' => 'Email должен быть действительным адресом электронной почты',
            'email.max' => 'Email не может превышать 255 символов',
            'email.unique' => 'Этот Email уже используется',
            
            'login.required' => 'Логин обязателен для заполнения',
            'login.string' => 'Логин должен быть строкой',
            'login.max' => 'Логин не может превышать 255 символов',
            'login.unique' => 'Этот логин уже используется',
            
            'phone.string' => 'Телефон должен быть строкой',
            'phone.max' => 'Телефон не может превышать 20 символов',
            
            'password.required' => 'Пароль обязателен для заполнения',
            'password.string' => 'Пароль должен быть строкой',
            'password.min' => 'Пароль должен содержать не менее 8 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'errors' => $validator->errors(),
            ], 422));
        }
    }
}
