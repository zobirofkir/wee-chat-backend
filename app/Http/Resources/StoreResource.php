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
            'theme_url' => $this->when($this->domain && $this->theme, function () {
                $protocol = app()->environment('local') ? 'http' : 'https';
                return "{$protocol}://{$this->domain}";
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'theme' => $this->theme,
            'preview_url' => url("storage/themes/user_{$this->user_id}/" . ($this->theme ?? 'default') . '/index.html'),
        ];
    }
}
