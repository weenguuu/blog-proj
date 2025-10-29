<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use App\Models\PostCategory;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Hidden;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;

/**
 * @extends ModelResource<Post>
 */
class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Посты';

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Заголовок', 'title')->sortable(),
            Text::make('ID категории', 'post_category_id'),
            Select::make('Статус', 'status')
                ->options([
                    10 => 'На модерации',
                    20 => 'Опубликован',
                    30 => 'Отклонен'
                ]),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        $categories = PostCategory::pluck('name', 'id')->toArray();

        return [
            Box::make([
                ID::make(),

                Text::make('Заголовок', 'title')->required(),
                Textarea::make('Текст', 'text')->required(),
                Select::make('Категория', 'post_category_id')
                    ->options($categories)
                    ->required()
                    ->searchable(),
                Select::make('Статус', 'status')
                    ->options([
                        10 => 'На модерации',
                        20 => 'Опубликован',
                        30 => 'Отклонен'
                    ])
                    ->default(10)
                    ->required(),
                Image::make('Изображение', 'image')
                    ->dir('posts')
                    ->disk('public'),
            ])
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Заголовок', 'title'),
            Textarea::make('Текст', 'text'),
            Text::make('ID категории', 'post_category_id'),
            Select::make('Статус', 'status')
                ->options([
                    10 => 'На модерации',
                    20 => 'Опубликован',
                    30 => 'Отклонен'
                ]),
            Image::make('Изображение', 'image'),
        ];
    }

    protected function rules(mixed $item): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'text' => ['required', 'string', 'min:10'],
            'post_category_id' => ['required', 'exists:post_categories,id'],
            'status' => ['required', 'integer'],
            'image' => ['sometimes', 'image', 'max:2048'],
        ];
    }

//    protected function topButtons(): \MoonShine\Support\ListOf
//    {
//        return parent::topButtons()->add(
//            ActionButton::make('Refresh', '#')
//                ->dispatchEvent(AlpineJs::event(JsEvent::TABLE_UPDATED, $this->getListComponentName()))
//        );
//    }
}
