<?php

namespace App\Http\Resources\V1\Role;

use App\Http\Resources\V1\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            'users' => UserResource::collection($this->users),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }

    public function withResponse($request, $response)
    {
        /**
         * Not all prerequisites were met.
         */
        $response->setStatusCode(428, 'Precondition Required');
    }


    public function with($request)
    {
        return ['status' => 'success'];
    }
}
