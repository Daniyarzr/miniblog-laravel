<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function __construct($resource, private bool $withContent = false)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'excerpt' => $this->when(!$this->withContent, fn () => mb_substr($this->content, 0, 150) . '...'),
            'content' => $this->when($this->withContent, $this->content),
            'author' => [
                'id' => $this->author?->id,
                'name' => $this->author?->name,
                'avatar_url' => $this->author?->avatar_url,
            ],
            'likes' => $this->likes_count ?? 0,
            'created_at' => $this->created_at?->toDateString(),
        ];
    }
}
