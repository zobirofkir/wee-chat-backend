<?php
namespace App\Services\Services\Store\Traits;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ThemeCustomizationResource;
use App\Http\Resources\ThemeCustomizationTraitResource;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\JsonResponse;

trait ThemeCustomizationTrait
{
    /**
     * Get customization file path
     *
     * @param int $userId
     * @param string $themeName
     * @return string
     */
    private function getCustomizationPath(int $userId, string $themeName) : string
    {
        return "themes/user_{$userId}/{$themeName}/customization.json";
    }

    /**
     * Get default customization options for a theme
     *
     * @param string $themeName
     * @return array
     */
    private function getDefaultCustomizationOptions(string $themeName) : array
    {
        return [
            'colors' => [
                'primary' => '#007bff',
                'secondary' => '#6c757d',
                'background' => '#ffffff',
                'text' => '#212529'
            ],
            'typography' => [
                'font_family' => 'Arial, sans-serif',
                'font_size' => '16px',
                'line_height' => '1.5'
            ],
            'layout' => [
                'container_width' => '1200px',
                'spacing' => '1rem'
            ]
        ];
    }

    /**
     * Update specific element in the file content
     *
     * @param string $content
     * @param string $section
     * @param string $element
     * @param string $newContent
     * @return string
     */
    private function updateSpecificElement(string $content, string $section, string $element, string $newContent) : string
    {
        /**
         * Find the section
         */
        $sectionPattern = "/<section[^>]*class=\"[^\"]*{$section}[^\"]*\"[^>]*>(.*?)<\/section>/s";
        if (preg_match($sectionPattern, $content, $sectionMatches)) {
            $sectionContent = $sectionMatches[1];

            /**
             * Find the element within the section
             */
            $elementPattern = "/<{$element}[^>]*>(.*?)<\/{$element}>/s";
            if (preg_match($elementPattern, $sectionContent)) {
                /**
                 * Replace the element content
                 */
                $content = preg_replace(
                    $elementPattern,
                    "<{$element}>{$newContent}</{$element}>",
                    $content
                );
            }
        }

        return $content;
    }

    /**
     * Get custom options
     *
     * @param string $path
     * @return array
     */
    private function getCustomOptions(string $path): array
    {
        return Storage::exists($path) ? json_decode(Storage::get($path), true) : [];
    }

    /**
     * Save custom options
     *
     * @param integer $userId
     * @param string $theme
     * @param array $options
     * @return JsonResponse
     */
    private function saveCustomization(int $userId, string $theme, array $options) : JsonResponse
    {
        $customizationPath = $this->getCustomizationPath($userId, $theme);
        Storage::put($customizationPath, json_encode($options, JSON_PRETTY_PRINT));

        return response()->json(ThemeCustomizationResource::make($options));
    }

    /**
     * Return error response
     *
     * @param string $message
     * @param integer $statusCode
     * @return JsonResponse
     */
    private function errorResponse(string $message, int $statusCode) : JsonResponse
    {
        return response()->json(ThemeCustomizationResource::error($message), $statusCode);
    }

    /**
     * Get theme data
     *
     * @param User $user
     * @param Store $store
     */
    private function getThemeData(User $user, Store $store)
    {
        return (new ThemeCustomizationTraitResource($user, $store))->forStore($user, $store);
    }

    protected function buildThemePath(int $userId, string $themeName): string
    {
        return "themes/user_{$userId}/{$themeName}";
    }

    protected function buildPreviewUrl(string $themePath): string
    {
        return url("storage/{$themePath}/index.html");
    }

    protected function getThemeInfo(string $themeInfoPath): array
    {
        if (!Storage::exists($themeInfoPath)) {
            return [];
        }

        $info = json_decode(Storage::get($themeInfoPath), true);

        return is_array($info) ? $info : [];
    }
}
