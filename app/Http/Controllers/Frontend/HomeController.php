<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;

class HomeController extends Controller
{
    public function index()
    {
        // Check if homepage has GrapesJS content
        $homepage = Page::withoutGlobalScopes()
            ->where('slug', 'home')
            ->where('post_type', 'page')
            ->first();

        if ($homepage && $homepage->grapes_data) {
            return view('frontend.home-builder', compact('homepage'));
        }

        // Fallback to default home view
        return view('frontend.home');
    }
}
