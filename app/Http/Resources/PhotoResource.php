<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PhotoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        $url = Cloudinary::getUrl($this->url);
        return [
            'url' => $url,
        ];
    }
}
