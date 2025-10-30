<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;



//???????Будут общие методы для всех контроллеров????????
class BaseApiController extends Controller
{
    /**
     * успешный json ответ
     */
    protected function successResponse($data = null,string $message = '',int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    /**
     * ошибка в json ответе
     */
    protected function errorResponse(string $message = '',$errors = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }


    /**
     * валидация запроса
     */
    protected function validateRequest(Request $request, array $rules): array|bool
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }
        return true;
    }

    /**
     * обработка добавления изображения
     */
    protected function handleImageUpload(Request $request, $existingPost = null): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        //  есть старое изображение - удаляем
        if ($existingPost && $existingPost->image) {
            Storage::disk('public')->delete($existingPost->image);
        }

        // cохраняем новое
        $path = $request->file('image')->store('posts', 'public');
        return $path;
    }
}
