<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            'name' => $this->name,
            'domain' => $this->domain,
            'is_active' => $this->is_active,
            'theme' => $this->theme,
            'theme_applied_at' => $this->theme_applied_at,
            'theme_storage_path' => $this->theme_storage_path,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
