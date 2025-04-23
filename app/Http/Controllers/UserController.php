<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;


class UserController extends Controller
{
    public function handleUserUpdate(Request $request)
{
    $request->validate([
        'id' => 'required|integer|exists:users,id',
        'last_name' => 'nullable|string|max:100',
        'first_name' => 'nullable|string|max:100',
        'middle_name' => 'nullable|string|max:100',
        'email' => 'nullable|email|unique:users,email,'.$request->id,
        'login' => 'nullable|string|max:50|unique:users,login,'.$request->id,
        'phone' => 'nullable|string|max:20',
        'user_type_id' => 'nullable|integer|exists:user_types,id',
        'active' => 'nullable|boolean',
        'password' => 'nullable|string|min:8|confirmed',
        'photo' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $user = User::findOrFail($request->id);
    $updateData = [];

    $fields = [
        'last_name', 'first_name', 'middle_name',
        'email', 'login', 'phone', 'user_type_id', 'active', 'password',
    ];

    foreach ($fields as $field) {
        if ($request->has($field)) {
            $updateData[$field] = $request->input($field);
        }
    }

    if ($request->filled('password')) {
        $updateData['password'] = Hash::make($request->password);
    }

    if (!empty($updateData)) {
        $user->update($updateData);
    }


    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $randomName = time() . '_' . $request->id . '.' . $file->getClientOriginalExtension();

        try {
            $client = new Client(['verify' => false]);

            $response = $client->post("http://10.11.7.251:8000/gate/upload/user/avatar/", [
                'multipart' => [
                    [
                        'name'     => 'files',
                        'contents' => fopen($file->path(), 'r'),
                        'filename' => $randomName
                    ]
                ]
            ]);



            $user->update(['photo' => "http://10.11.7.251:8000/avatars/$randomName"]);

            return response()->json([
                'success' => true,
                'message' => 'Данные пользователя, фото и пароль успешно обновлены',
                'data' => $user->fresh(),
            ]);

        } catch (BadResponseException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $responseBody = json_decode($e->getResponse()->getBody(), true);

            return response()->json($responseBody ?? [
                'message' => 'Ошибка сервера при загрузке фото'
            ], $statusCode);

        } catch (ConnectException $e) {
            return response()->json([
                'message' => 'Не удалось подключиться к серверу загрузки изображений'
            ], 500);

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $responseBody = json_decode($e->getResponse()->getBody(), true);
                return response()->json(
                    $responseBody ?? ['message' => 'Ошибка запроса при загрузке фото'],
                    $statusCode
                );
            }

            return response()->json([
                'message' => 'Ошибка при отправке запроса загрузки фото'
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Внутренняя ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Данные пользователя успешно обновлены',
        'data' => $user->fresh(),
    ]);
}


public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required|string',
        'new_password' => [
            'required',
            'string',
            'confirmed',
            Password::min(8)
                //->mixedCase()
                //->numbers()
                //->symbols(),
        ],
    ]);

    $user = auth()->user();

    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json(['error' => 'Текущий пароль неверный'], 401);
    }


    $user->update([
        'password' => Hash::make($request->new_password)
    ]);

    return response()->json(['message' => 'Пароль успешно изменен']);
}
}
