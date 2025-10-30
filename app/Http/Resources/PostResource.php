<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        $request->user();
//        auth()->user();
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'text' => $this->text,
            'category_id' => $this->post_category_id,
            'category_name' => $this->category->name ?? null,
            'status' => $this->status,
            'status_text' => $this->getStatusLabel(),
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'created_at' => $this->created_at->format('d.m.Y H:i'),
            ];
    }
}
