<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThemeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
                'name' => $this->resource['name'],
                'applied_at' => $this->resource['applied_at'],
                'storage_path' => $this->resource['storage_path'],
                'preview_url' => $this->resource['preview_url'],
                'info' => $this->resource['info']
        ];
    }
}
