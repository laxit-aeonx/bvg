<?php

namespace App\Http\Resources\V1\Project;

use App\Http\Resources\V1\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'name' => $this->name,
            'created_at' => $this->created_at->diffForHumans(),
            'id' => $this->id,
            'database' => [
                'db_name' => $this->db_name,
                'db_user' => $this->db_user
            ]
        ];
    }
}
