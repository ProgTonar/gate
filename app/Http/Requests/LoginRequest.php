<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'login' => 'required|string',
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'login.required' => 'Логин обязателен для заполнения',
            'login.string' => 'Логин должен быть строкой',
            'login.min' => 'Логин должен содержать хотя бы 1 символ',
            'login.max' => 'Логин не может превышать 255 символов',
            'login.exists' => 'Такого логина не существует',

            'password.required' => 'Пароль обязателен для заполнения',
            'password.string' => 'Пароль должен быть строкой',
            'password.min' => 'Пароль должен содержать хотя бы 1 символ',
            'password.max' => 'Пароль не может превышать 255 символов',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }
}
