<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request):array
    {
        return [
            'id'=> $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'address' => $this->address,
            'address_link' => $this->address_link,
            'image_placeholder' => $this->image_placeholder,
            'image_gallery' => $this->image_gallery,
            'user_id' => $this->user_id,
        ];
    }
}
