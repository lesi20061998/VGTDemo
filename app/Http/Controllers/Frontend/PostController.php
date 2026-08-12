<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::posts()
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('frontend.posts.index', compact('posts'));
    }

    public function show($locale = null, $slug = null)
    {
        // Handle both localized and non-localized routes
        if ($slug === null) {
            $slug = $locale;
            $locale = null;
        }

        $post = Post::posts()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $tocService = new \App\Services\TocService();
        $tocData = $tocService->generate($post->content);
        $post->content = $tocData['content'];
        $post->toc = $tocData['toc'] ?? [];
        $post->toc_html = $tocData['html'] ?? '';

        return view('frontend.posts.show', compact('post'));
    }
}
