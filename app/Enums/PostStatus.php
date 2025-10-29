<?php

namespace App\Enums;

enum PostStatus: int
{
    case BRANDNEW = 10;
    case PUBLISHED = 20;
    case REJECTED = 30;

    public function label(): string
    {
        return match ($this) {
            self::BRANDNEW => 'Модерируем...',
            self::PUBLISHED => 'Опубликован',
            self::REJECTED => 'Отклонен'
        };
    }

    public static function options(): array
    {
        return [
            self::BRANDNEW->value => self::BRANDNEW->label(),
            self::PUBLISHED->value => self::PUBLISHED->label(),
            self::REJECTED->value => self::REJECTED->label(),
        ];
    }


}
