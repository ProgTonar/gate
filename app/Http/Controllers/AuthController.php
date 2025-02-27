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
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Passport\Client as PassportClient;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AuthController extends Controller
{
    public function registration(RegistrationRequest $request)
    {
        try{

            User::create([
                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'email' => strtolower($request->email) ?? null,
                'login' => strtolower($request->login),
                'password' => Hash::make($request->password),
            ]);

            return response()->json(['message' => 'Пользователь создан'], 201);
        }catch(QueryException $e) {
            return response()->json($e->getMessage(), 500);
        } catch(Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function redirect(Request $request)
    {
        $name = $request->input('name');

        $client = PassportClient::where('name', $name)->first();

        if(!$client) {
            return redirect(config('app.url'))->with('Клиент не найден');
        }

        $state = Str::random(40);
        $codeVerifier = Str::random(128);

        $codeChallenge = strtr(rtrim(
            base64_encode(hash('sha256', $codeVerifier, true))
        , '='), '+/', '-_');

        session()->put('state',$state);
        session()->put('codeChallenge',$codeChallenge);
        session()->put('codeVerifier',$codeVerifier);
        session()->put('name',$client->name);

        // Создаем query параметры для редиректа
        $query = http_build_query([
            'client_id' => $client->id,
            'redirect_uri' => $client->redirect,
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
            'prompt' => 'blind',
        ]);

        return redirect(config('app.url') . '/oauth/authorize?' . $query);
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->login)->orWhere('login', $request->login)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Неверные учетные данные'], 401);
            }

            $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'login';
            $credentials = [
                $field => $request->login,
                'password' => $request->password,
            ];

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Неверные учетные данные'], 401);
            }

            $link = $this->localRedirect();

            return response()->json(['link' => $link]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function localRedirect()
    {
        $state = session()->get('state');
        $codeChallenge = session()->get('codeChallenge');
        $name = session()->get('name');

        $client = PassportClient::where('name', $name)->first();

        if (!$client) {
            return response()->json(['error' => 'Клиент не найден'], 404);
        }
        $query = http_build_query([
            'client_id' => $client->id,
            'redirect_uri' => $client->redirect,
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
            'prompt' => 'blind',
        ]);
        $authorizationUrl = config('app.url') . '/oauth/authorize?' . $query;

        return $authorizationUrl;
    }

    public function callback(Request $request)
    {
        $code = $request->input('code');
        $codeVerifier = session()->pull('codeVerifier');
        $name = session()->pull('name');

        $client = PassportClient::where('name', $name)->first();
        if (!$client) {
            return response()->json(['error' => 'Клиент не найден'], 404);
        }

        try {
            $http = new Client();
            $response = $http->post(config('app.url') . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $client->id,
                    'redirect_uri' => $client->redirect,
                    'code_verifier' => $codeVerifier,
                    'code' => $code,
                ],
            ]);

            $responseData = json_decode((string) $response->getBody(), true);

            return response()->json($responseData);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ошибка авторизации: ' . $e->getMessage()], 500);
        }
    }
}
