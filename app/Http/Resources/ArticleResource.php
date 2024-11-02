<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\V1\S3Controller;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class ArticleResource extends JsonResource
{


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'title'=>$this->title ?? 'عموان موجود نیست',
            'body'=>$this->body ?? 'متن موجود نیست',
            'creator'=>$this->creator ?? 'no',
            'upload_file' =>$this->upload_file ?? 'no',
            'review'=>$this->review,
            'comments'=>CommentResource::collection($this->comments),
            'cover'=>$this->cover,

        ];
    }
}
