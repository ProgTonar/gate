<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    public function loginPage(Request $request)
    {
        // Получаем URL для редиректа из параметра запроса
        $redirectUrl = $request->query('redirect_url', env('FRONTEND_URL'));

        // Добавим логирование для отладки
        Log::info('Login page accessed. Redirect URL: ' . $redirectUrl);

        return view('auth.login', ['redirectUrl' => $redirectUrl]);
    }

    public function registrationPage(Request $request)
    {
        // Получаем URL для редиректа из параметра запроса
        $redirectUrl = $request->query('redirect_url', env('FRONTEND_URL'));

        // Добавим логирование для отладки
        Log::info('Registration page accessed. Redirect URL: ' . $redirectUrl);

        return view('auth.registration', ['redirectUrl' => $redirectUrl]);
    }

    public function dashboardPage(Request $request)
    {
        return view('dashboard.dashboard', []);
    }
}
