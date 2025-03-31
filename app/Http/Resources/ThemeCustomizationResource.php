<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThemeCustomizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'options' => $this->resource
        ];
    }

    /**
     * Transform the resource into a response with error.
     *
     * @param string $message
     * @param int $status
     * @return array<string, mixed>
     */
    public static function error(string $message, int $status = 404): array
    {
        return [
            'success' => false,
            'message' => $message
        ];
    }
}
