<?php

namespace App\Http\Resources;

use App\Models\Store;
use App\Models\User;
use App\Services\Services\Store\Traits\ThemeCustomizationTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThemeCustomizationTraitResource extends JsonResource
{
    use ThemeCustomizationTrait;

    public function forStore(User $user, Store $store)
    {
        $themePath = $this->buildThemePath($user->id, $store->theme);
        $themeInfoPath = "{$themePath}/theme-info.json";

        return new self ([
            'name' => $store->theme,
            'applied_at' => $store->theme_applied_at,
            'storage_path' => $store->theme_storage_path,
            'preview_url' => $this->buildPreviewUrl($themePath),
            'info' => $this->getThemeInfo($themeInfoPath),
        ]);
    }
}
