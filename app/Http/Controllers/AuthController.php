<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return response()->json(['message' => 'Пользователь успешно создан'], 201);
        }catch(ValidationException $e){
            return response()->json(['message' => $e->getMessage()], 422);
        }catch(QueryException $e) {
            Log::error("Произошла ошибка c БД в \"AuthController\" функция \"register\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch(Exception $e) {
            Log::error("Произошла ошибка в \"AuthController\" функция \"register\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }

    public function login(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $http = new Client;

            $response = $http->post(config('app.url') . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $request->email,
                    'password' => $request->password,
                    'scope' => '',
                ],
            ]);

            return json_decode((string) $response->getBody(), true);
        }catch(ValidationException $e){
            return response()->json(['message' => $e->getMessage()], 422);
        }catch(QueryException $e) {
            Log::error("Произошла ошибка c БД в \"AuthController\" функция \"login\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        } catch(Exception $e) {
            Log::error("Произошла ошибка в \"AuthController\" функция \"login\":" . $e->getMessage());
            return response()->json(['message' => 'Произошла ошибка, обратитесь в службу поддержки'], 500);
        }
    }
}
