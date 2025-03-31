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
            'domain' => $this->when($this->store,
                fn() => "{$this->store->domain}/storage/themes/user_{$this->id}/{$this->store->theme}/index.html"
            ),
        ];
    }
}
