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

        Log::info('redirect(получаем): ' . $name);

        $client = PassportClient::where('name', $name)->first();

        if($client) {
            session()->put('redirect', $client->redirect);
            Log::info('redirect(достаем урл): ' . $client->redirect);
        }

        return redirect(route('index'));
    }

    public function login(LoginRequest $request)
    {
        try {

            $url = session()->pull('redirect');

            Log::info('Login(получаем урл): ' . $url);
            Log::info('Login(айди): ' . config('app.client_id'));
            Log::info('Login(айди): ' . config('app.client_secret'));
            Log::info('Login(логин): ' . $request->login);
            Log::info('Login(пароль): ' . $request->password);

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

            $fullLink = $url . '?' . $tokens;

            return response()->json(['url' => $fullLink]);
        }catch(BadResponseException $e){
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}