<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($locale = null, $slug = null)
    {
        // Handle both localized and non-localized routes
        if ($slug === null) {
            $slug = $locale;
            $locale = null;
        }

        $page = Post::pages()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('frontend.page', compact('page'));
    }

    public function contact($locale = null)
    {
        return view('frontend.contact');
    }

    public function contactSubmit(Request $request, $locale = null)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        return back()->with('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.');
    }
}
