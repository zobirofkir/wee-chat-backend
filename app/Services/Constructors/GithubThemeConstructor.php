<?php

namespace App\Services\Constructors;

interface GithubThemeConstructor
{
    /**
     * List All Themes
     *
     * @return void
     */
    public function index();

    /**
     * Generate a test URL for a theme
     *
     * @param [type] $themeName
     * @return void
     */
    public function generateTestUrl($themeName);
}
