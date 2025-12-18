<?php

namespace Modules\SystemSetting\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->mainImage ? asset('storage/' . $this->mainImage->path) : null,
            'category' => new BlogCategoryResource($this->blogCategory),
            'views_count' => $this->views_count,
            'comments_count' => $this->comments_count,
            'comments' => BlogCommentResource::collection($this->whenLoaded('comments')),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->getMetaKeywordsArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
