<?php

namespace App\Http\Resources\API\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'           => (string)$this->id,
            'firstname'    => $this->firstname,
            'lastname'     => $this->lastname,
            'email'        => $this->email,
            'phone_number' => $this->phone_number,
            'is_suspended' => $this->is_suspended,
            'last_login'   => $this->last_login,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
