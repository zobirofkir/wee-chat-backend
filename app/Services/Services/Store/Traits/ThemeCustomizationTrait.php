<?php
namespace App\Services\Services\Store\Traits;
use Illuminate\Support\Facades\Storage;

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
}
