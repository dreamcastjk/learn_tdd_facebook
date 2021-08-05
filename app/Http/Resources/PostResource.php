<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    #[ArrayShape(['data' => "array", 'links' => "array"])]
    public function toArray($request): array
    {
        return [
            'data' => [
                'type' => 'posts',
                'post_id' => $this->id,
                'attributes' => [
                    'posted_by' => new UserResource($this->user),
                    'body' => $this->body,
                ],
            ],
            'links' => [
                'self' => url('/posts/'.$this->id)
            ],
        ];
    }
}
