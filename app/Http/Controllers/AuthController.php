<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Database\QueryException;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\RegistrationRequest;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Request;
use Laravel\Passport\Client as PassportClient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function registration(RegistrationRequest $request)
    {
        try {
            // Проверяем, существует ли пользователь с таким логином
            $existingUser = User::where('login', strtolower($request->login))->first();
            if ($existingUser) {
                if (!$request->expectsJson()) {
                    return redirect()->back()
                        ->with('error', 'Пользователь с таким логином уже существует')
                        ->withInput();
                }
                return response()->json([
                    'message' => 'Пользователь с таким логином уже существует',
                    'errors' => ['login' => ['Этот логин уже используется']]
                ], 422);
            }

            // Проверяем, существует ли пользователь с таким email (если email указан)
            if ($request->email) {
                $existingEmail = User::where('email', strtolower($request->email))->first();
                if ($existingEmail) {
                    if (!$request->expectsJson()) {
                        return redirect()->back()
                            ->with('error', 'Пользователь с таким email уже существует')
                            ->withInput();
                    }
                    return response()->json([
                        'message' => 'Пользователь с таким email уже существует',
                        'errors' => ['email' => ['Этот email уже используется']]
                    ], 422);
                }
            }


            $user = User::create([
                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'email' => $request->email ? strtolower($request->email) : null,
                'login' => strtolower($request->login),
                'password' => Hash::make($request->password),
                'phone' => $request->phone ?? null,
            ]);


            $redirectUrl = $request->input('redirect_url', env('FRONTEND_URL'));


            $http = new Client();
            $response = $http->post(config('app.url') . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('app.client_id'),
                    'client_secret' => config('app.client_secret'),
                    'username' => $request->login,
                    'password' => $request->password,
                    'scope' => '',
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $tokens = http_build_query($data);
            $fullLink = $redirectUrl . '?' . $tokens;


            if (!$request->expectsJson()) {
                return redirect($fullLink);
            }


            return response()->json([
                'message' => 'Пользователь создан',
                'url' => $fullLink
            ], 201);
        } catch (QueryException $e) {
            Log::error('Registration error (QueryException): ' . $e->getMessage());


            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $errorMessage = 'Пользователь с таким логином или email уже существует';

                if (!$request->expectsJson()) {
                    return redirect()->back()->with('error', $errorMessage)->withInput();
                }

                return response()->json([
                    'message' => $errorMessage,
                    'errors' => ['login' => ['Этот логин или email уже используется']]
                ], 422);
            }

            if (!$request->expectsJson()) {
                return redirect()->back()->with('error', 'Ошибка при регистрации: ' . $e->getMessage())->withInput();
            }

            return response()->json(['message' => $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('Registration error (Exception): ' . $e->getMessage());

            if (!$request->expectsJson()) {
                return redirect()->back()->with('error', 'Ошибка при регистрации: ' . $e->getMessage())->withInput();
            }

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function redirect(Request $request)
    {
        $name = $request->input('name');

        Log::info('redirect(получаем): ' . $name);

        $client = PassportClient::where('name', $name)->first();

        if ($client) {
            session()->put('redirect', $client->redirect);
            Log::info('redirect(достаем урл): ' . $client->redirect);
        }

        return redirect(route('index'));
    }

    public function login(LoginRequest $request)
    {
        try {
            // Проверка доступности порта перед запросом
            $isPortOpen = @fsockopen(parse_url(config('app.url'), PHP_URL_HOST), 9091, $errno, $errstr, 2);
            if (!$isPortOpen) {
                throw new Exception("OAuth server is not reachable: $errstr ($errno)");
            }
            fclose($isPortOpen);

            $oauthUrl = config('app.url') . '/oauth/token';
            Log::info('Attempting to connect to: ' . $oauthUrl);

            $response = Http::asForm()->post($oauthUrl, [
                'grant_type' => 'password',
                'client_id' => config('app.client_id'),
                'client_secret' => config('app.client_secret'),
                'username' => $request->login,
                'password' => $request->password,
                'scope' => '',
            ]);

            if ($response->failed()) {
                Log::error('OAuth server error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new Exception($response->json()['message'] ?? 'Authentication failed');
            }

            $data = $response->json();
            $redirectUrl = session()->pull('redirect', env('FRONTEND_URL'));

            return response()->json([
                'redirect_url' => $redirectUrl . '?' . http_build_query($data),
                'tokens' => $data
            ]);
        } catch (ConnectException $e) {
            Log::error('Connection to OAuth server failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Не удалось подключиться к серверу аутентификации'
            ], 503);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Ошибка входа: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed: ' . json_encode($validator->errors()));
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $redirectUrl = $request->input('redirect_url', env('FRONTEND_URL'));
            Log::info('Process login. Redirect URL: ' . $redirectUrl);

            // Проверка доступности OAuth сервера
            $host = parse_url(config('app.url'), PHP_URL_HOST);
            $port = parse_url(config('app.url'), PHP_URL_PORT) ?? 9091;
            $isPortOpen = @fsockopen($host, $port, $errno, $errstr, 2);

            if (!$isPortOpen) {
                Log::error('OAuth server connection failed', [
                    'host' => $host,
                    'port' => $port,
                    'error' => "$errstr ($errno)"
                ]);
                throw new Exception("Сервер аутентификации недоступен. Пожалуйста, попробуйте позже.");
            }
            fclose($isPortOpen);

            $oauthUrl = config('app.url') . '/oauth/token';
            Log::info('Attempting to get token from: ' . $oauthUrl);

            $response = Http::asForm()->post($oauthUrl, [
                'grant_type' => 'password',
                'client_id' => config('app.client_id'),
                'client_secret' => config('app.client_secret'),
                'username' => $request->login,
                'password' => $request->password,
                'scope' => '',
            ]);

            if ($response->failed()) {
                Log::error('OAuth error response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new Exception('Неверный логин или пароль');
            }

            $tokenData = $response->json();
            Log::info('Token response: ' . json_encode($tokenData));

            $userInfoUrl = config('app.url') . '/api/user';
            $userResponse = Http::withToken($tokenData['access_token'])->get($userInfoUrl);

            if ($userResponse->failed()) {
                Log::error('Failed to fetch user info', [
                    'status' => $userResponse->status(),
                    'body' => $userResponse->body(),
                ]);
                throw new Exception('Не удалось получить информацию о пользователе');
            }

            $userData = $userResponse->json();
            Log::info('User info: ' . json_encode($userData));

            $responseData = array_merge($tokenData, [
                'user' => [
                    'id' => $userData['id'],
                    'login' => $userData['login'],
                    'first_name' => $userData['first_name'],
                ]
            ]);

            return redirect($redirectUrl . '?' . http_build_query($responseData));
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            Log::error('OAuth error: ' . $e->getMessage() . ' | ' . $response->getBody());
            return redirect()->back()
                ->with('error', 'Неверный логин или пароль')
                ->withInput();
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}
