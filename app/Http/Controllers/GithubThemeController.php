<?php

namespace App\Http\Controllers;

use App\Services\Facades\GithubThemeFacade;
use Illuminate\Http\Request;

class GithubThemeController extends Controller
{
    public function index()
    {
        return GithubThemeFacade::index();
    }
}
