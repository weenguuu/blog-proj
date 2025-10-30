<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseApiController
{
    /**
     * POST /api/user/login
     * авторизация пользователя и выдача токена
     */
    public function login(Request $request)
    {
        $validator = $this->validateRequest($request, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator !== true) {
            return $this->errorResponse('Ошибка валидации', $validator, 422);
        }

        // Ищем пользователя по email
        $user = User::where('email', $request->email)->first();

        // проверяем существует ли пользователь и правильный ли пароль
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Неверный email или пароль', null, 401);
        }

        // Создаём токен для пользователя
        $token = $user->createToken('api-token')->plainTextToken;

        // Возвращаем успешный ответ с токеном
        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 'Успешная авторизация');
    }
}
