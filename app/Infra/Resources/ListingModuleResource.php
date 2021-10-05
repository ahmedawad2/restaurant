<?php

namespace App\Infra\Resources;

use App\Infra\Interfaces\Resources\ResourceInterface;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingModuleResource extends JsonResource implements ResourceInterface
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image,
            'description' => $this->description,
        ];
    }

    public function get($input)
    {
        // TODO: Implement get() method.
    }
}
