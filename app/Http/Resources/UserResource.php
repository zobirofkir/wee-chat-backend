<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'phone' => $this->phone,
            'avatar' => asset('storage/' . $this->avatar),
            'account_type' => $this->account_type,
            'location' => $this->location,
            'preview_url' => "http://0.0.0.0/storage/themes/user_{$this->id}/cozastore/index.html",
        ];
    }
}
