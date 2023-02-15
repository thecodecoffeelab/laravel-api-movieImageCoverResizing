<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class MovieManipulationResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'original' => URL::to($this->path),
            'output' => URL::to($this->output_path),
            'movie_id' => $this->movie_id,
            'created_at' => $this->created_at,
        ];
    }
}
