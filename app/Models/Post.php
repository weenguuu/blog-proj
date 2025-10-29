<?php

namespace App\Models;

use App\PostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Termwind\terminal;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','title', 'text', 'post_category_id', 'status','image'];

    public function getStatus():PostStatus
    {
        return PostStatus::from($this->status);
    }

    public function getStatusLabel():string{
        return $this -> getStatus() -> label();
    }

    //проверка конкретных статусов
    public function isBrandNew(): bool
    {
        return $this->status === PostStatus::BRANDNEW->value;
    }
    public function isPublished(): bool
    {
        return $this->status === PostStatus::PUBLISHED->value;
    }
    public function isRejected(): bool
    {
        return $this->status === PostStatus::REJECTED->value;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->user_id)) {
                $post->user_id = auth()->check() ? auth()->id() : 1;
            }
        });
    }
}
