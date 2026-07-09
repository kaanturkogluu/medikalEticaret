<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'name' => $this->name,
            'phone' => $this->phone,
            'province' => $this->province,
            'district' => $this->district,
            'neighborhood' => $this->neighborhood,
            'address' => $this->address,
            'zip_code' => $this->zip_code,
            'is_default' => (bool) $this->is_default,
        ];
    }
}
