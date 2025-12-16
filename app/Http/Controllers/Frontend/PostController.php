<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::where('status', 'published')->orderBy('created_at', 'desc')->get();
        
        if ($posts->isEmpty()) {
            $posts = collect([
                (object)['title' => 'Getting Started with Modern Web Development', 'slug' => 'getting-started-web-dev', 'image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800', 'content' => '<p>Learn the fundamentals of modern web development.</p>'],
                (object)['title' => '10 Tips for Better Code Quality', 'slug' => '10-tips-code-quality', 'image' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800', 'content' => '<p>Discover proven strategies to write cleaner code.</p>'],
                (object)['title' => 'The Future of AI in Business', 'slug' => 'future-ai-business', 'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800', 'content' => '<p>Explore how AI is transforming industries.</p>'],
            ]);
        }
        
        return view('frontend.posts.index', compact('posts'));
    }
    
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->first();
        
        if (!$post) {
            $post = (object)[
                'title' => 'Getting Started with Modern Web Development',
                'slug' => $slug,
                'image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800',
                'content' => '<h2>Introduction</h2><p>Learn the fundamentals of modern web development with our comprehensive guide covering HTML, CSS, JavaScript, and popular frameworks.</p><h3>Key Topics</h3><ul><li>HTML5 & Semantic Markup</li><li>CSS3 & Responsive Design</li><li>JavaScript ES6+</li><li>React & Vue.js</li></ul>'
            ];
        }
        
        return view('frontend.posts.show', compact('post'));
    }
}

