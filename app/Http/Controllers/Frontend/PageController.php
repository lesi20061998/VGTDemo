<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = (object)[
            'title' => ucfirst(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'banner' => 'https://images.unsplash.com/photo-1557683316-973673baf926?w=1200',
            'content' => '<h2>Welcome to ' . ucfirst($slug) . '</h2><p>This is a dynamic page content. You can customize this content through the admin panel.</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>'
        ];
        
        return view('frontend.page', compact('page'));
    }
    
    public function contact()
    {
        return view('frontend.contact');
    }
    
    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);
        
        return back()->with('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.');
    }
}

