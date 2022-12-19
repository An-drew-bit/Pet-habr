<?php

namespace App\Http\Resources\Api\Article;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'user_name' => $this->user->nickName ?? 'Без автора',
            'avatar' => $this->user->avatar ?? null,
            'views' => $this->views,
            'likes' => $this->likes()->count(),
            'count_comments' => $this->comments()->count(),
            'image' => $this->when($this->image, fn() => $this->image),
            'created_at' => $this->setDate($this->created_at),
        ];
    }
}
