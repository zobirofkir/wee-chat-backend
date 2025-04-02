<?php
namespace App\Services\Services\Store\Traits;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

trait StoreServiceTrait
{
    /**
     * Get theme storage path
     *
     * @param int $userId
     * @param string $themeName
     * @return string
     */
    private function getThemeStoragePath(int $userId, string $themeName) : string
    {
        return "themes/user_{$userId}/{$themeName}";
    }

    /**
     * Configure domain for the store
     *
     * @param Store $store
     * @return void
     */
    private function configureDomain(Store $store) : void
    {
        if (app()->environment('local')) {
            Log::info("Store domain configured for local environment: {$store->domain}");
            return;
        }

        Log::info("Store domain configured for production: {$store->domain}");
    }

    /**
     * Remove old theme from storage
     *
     * @param int $userId
     * @param string $themeName
     * @return bool
     */
    private function removeOldTheme(int $userId, string $themeName) : bool
    {
        $storagePath = $this->getThemeStoragePath($userId, $themeName);

        if (Storage::disk('public')->exists($storagePath)) {
            return Storage::disk('public')->deleteDirectory($storagePath);
        }

        return false;
    }

    /**
     * Download file content from GitHub
     *
     * @param string $url
     * @return string|null
     */
    private function downloadFileContent(string $url) : ?string
    {
        $response = Http::get($url);
        if ($response->successful()) {
            return $response->body();
        }
        return null;
    }

    /**
     * Download a directory and its contents recursively
     *
     * @param int $userId
     * @param string $themeName
     * @param string $path
     * @return bool
     */
    private function downloadDirectory(int $userId, string $themeName, string $path) : bool
    {
        $storagePath = $this->getThemeStoragePath($userId, $themeName);
        $relativePath = str_replace($themeName . '/', '', $path);

        $response = Http::get("https://api.github.com/repos/zobirofkir/wee-build-themes/contents/{$path}");

        if ($response->successful()) {
            $contents = $response->json();

            foreach ($contents as $item) {
                $itemPath = $storagePath . '/' . $relativePath . '/' . $item['name'];

                if ($item['type'] === 'file') {
                    $fileContent = $this->downloadFileContent($item['download_url']);
                    if ($fileContent) {
                        Storage::disk('public')->put($itemPath, $fileContent);
                    }
                } elseif ($item['type'] === 'dir') {
                    Storage::disk('public')->makeDirectory($itemPath, 0755, true, true);
                    $this->downloadDirectory($userId, $themeName, $item['path']);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Get all files in a theme directory
     *
     * @param string $path
     * @return array
     */
    private function getThemeFiles(string $path) : array
    {
        $files = [];

        if (!Storage::disk('public')->exists($path)) {
            return $files;
        }

        $allFiles = Storage::disk('public')->allFiles($path);

        foreach ($allFiles as $file) {
            if (basename($file) === 'theme-info.json') {
                continue;
            }

            $relativePath = str_replace($path . '/', '', $file);
            $files[] = [
                'name' => basename($file),
                'path' => $relativePath,
                'full_path' => $file,
                'size' => Storage::disk('public')->size($file),
                'type' => 'file'
            ];
        }

        return $files;
    }


    /**
     * Generate store name
     *
     * @param User $user
     * @return string
     */
    private function generateStoreName(User $user) : string
    {
        return "Store of " . $user->username;
    }

    private function generateDomain(User $user) : string
    {
        $baseDomain = app()->environment('local') ? 'localhost' : 'wee-build.com';
        return Str::slug($user->username) . ".$baseDomain";
    }
}
