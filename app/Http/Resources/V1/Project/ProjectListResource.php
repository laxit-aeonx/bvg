<?php

namespace App\Http\Resources\V1\Project;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectListResource extends JsonResource
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
            'slug' => $this->slug,
            'created_at' => $this->created_at->diffForHumans()
        ];
    }

    public function withResponse($request, $response)
    {
        /**
         * Not all prerequisites were met.
         */
        $response->setStatusCode(428, 'Precondition Required');
    }
}
