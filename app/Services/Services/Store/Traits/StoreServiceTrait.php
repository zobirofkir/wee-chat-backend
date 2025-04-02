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
     * Generate store name
     *
     * @param User $user
     * @return string
     */
    private function generateStoreName(User $user) : string
    {
        return "Store of " . $user->username;
    }

    /**
     * Generate store domain
     *
     * @param User $user
     * @return string
     */
    private function generateDomain(User $user) : string
    {
        $baseDomain = app()->environment('local') ? 'localhost' : 'wee-build.com';
        return Str::slug($user->username) . ".$baseDomain";
    }
}
