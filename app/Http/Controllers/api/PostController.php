<?php

namespace App\Http\Controllers\Api;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends BaseApiController
{
    /**
     * GET-запрос /api/posts
     * получение опубликованных постов с фильтрацией
     */
    public function index(Request $request)
    {
        try{
            //сортировка по дате
            $query = Post::where('status', PostStatus::PUBLISHED->value)
                        ->with('category')
                        ->orderBy('created_at', 'desc');
            //фильтр по id поста
            if ($request->has('id')) {
                $query->where('id', $request->id);
            }

            //фильтр по id пользователя
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            //фильтр по категории
            if ($request->has('category_id')) {
                $query->where('post_category_id', $request->category_id);
            }

            //страницы
            $perPage = $request->get('item_count', 15); // По умолчанию 15 на страницу
            $page = $request->get('first_item', 1); // Начинаем с первой страницы

            $posts = $query->paginate($perPage, ['*'], 'page', $page);

            $formatedPosts = $posts->map(function ($post) {
                return $this->formatPost($post);
            });

            return $this->successResponse([
                'posts' => $formatedPosts,
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                    'last_page' => $posts->lastPage(),
                ]
            ], 'Посты получили!!!');

        } catch (\Exception $e){
            return $this->errorResponse('Ошибка при получении постоу', null, 500);
        }
    }

    /**
     * GET-запрос /api/posts/{id}
     * получение конкретного поста по id
     */
    public function show($id)
    {
        try {
            $post = Post::with('category')
                ->where('status', PostStatus::PUBLISHED->value)
                ->find($id);

            if (!$post) {
                return $this->errorResponse('Пост не найден', null, 404);
            }

            return $this->successResponse(
                $this->formatPost($post),
                'Пост успешно получен!'
            );
        }catch(\Exception $e){
            return $this->errorResponse('Ошибка при получении поста', null, 500);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = $this->validateRequest($request, [
                'title' => 'required|string|max:255',
                'text' => 'required|string|min:10',
                'category_id' => 'required|exists:post_category,id',
                'image' => 'sometimes|image|max:2048',
            ]);

            if ($validator !== true) {
                return $this->errorResponse('Ошибка валидации', $validator, 422);
            }

            $post = Post::create([
                'user_id' => 1, // Временно для тестирования
                'title' => $request->title,
                'text' => $request->text,
                'post_category_id' => $request->category_id,
                'status' => PostStatus::BRANDNEW->value,
                'image' => $this->handleImageUpload($request),
            ]);

            $post->load('category');

            return $this->successResponse(
                $this->formatPost($post),
                'Пост успешно создан и отправлен на модерацию',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Ошибка при создании поста: ' . $e->getMessage(), null, 500);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Находим пост
            $post = Post::find($id);

            if (!$post) {
                return $this->errorResponse('Пост не найден', null, 404);
            }

            // Проверяем что пост принадлежит текущему пользователю
            if ($post->user_id !== (auth()->id() ?? 1)) {
                return $this->errorResponse('Нельзя редактировать чужие посты', null, 403);
            }

            // Валидация
            $validator = $this->validateRequest($request, [
                'title' => 'sometimes|string|min:3|max:255',
                'text' => 'sometimes|string|min:10',
                'category_id' => 'sometimes|exists:post_categories,id',
                'image' => 'sometimes|image|max:2048',
            ]);

            if ($validator !== true) {
                return $this->errorResponse('Ошибка валидации', $validator, 422);
            }

            // Обновляем только переданные поля
            $post->update([
                'title' => $request->title ?? $post->title,
                'text' => $request->text ?? $post->text,
                'post_category_id' => $request->category_id ?? $post->post_category_id,
                'image' => $request->hasFile('image') ? $this->handleImageUpload($request, $post) : $post->image,
            ]);

            $post->load('category');

            return $this->successResponse(
                $this->formatPost($post),
                'Пост успешно обновлен'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('Ошибка при обновлении поста', null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $post = Post::find($id);

            if (!$post) {
                return $this->errorResponse('Пост не найден', null, 404);
            }

            // Проверяем что пост принадлежит текущему пользователю
            if ($post->user_id !== (auth()->id() ?? 1)) {
                return $this->errorResponse('Нельзя удалять чужие посты', null, 403);
            }

            $post->delete();

            return $this->successResponse(
                null,
                'Пост успешно удален'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('Ошибка при удалении поста', null, 500);
        }
    }
}
