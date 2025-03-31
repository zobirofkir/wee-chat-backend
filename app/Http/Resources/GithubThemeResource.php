<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GithubThemeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'] ?? null,
            'name' => $this->resource['name'] ?? null,
            'path' => $this->resource['path'] ?? null,
            'url' => $this->resource['url'] ?? null,
            'test_url' => $this->resource['test_url'] ?? null,
            'type' => $this->resource['type'] ?? null,
            'category' => $this->resource['category'] ?? null,
            'files' => $this->resource['files'] ?? null,
            'preview_url' => $this->resource['preview_url'] ?? null,
            'theme_storage_path' => $this->resource['theme_storage_path'] ?? null,
            'theme_applied_at' => $this->resource['theme_applied_at'] ?? null,
        ];
    }
}
