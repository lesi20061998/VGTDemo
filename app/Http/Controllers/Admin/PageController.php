<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $pages = Post::pages()
            ->with('author')
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('cms.pages.index', compact('pages'));
    }

    public function create()
    {
        return redirect()->route('cms.posts.create', ['type' => 'page']);
    }

    public function store(Request $request)
    {
        return redirect()->route('cms.posts.store');
    }

    public function show(Post $page)
    {
        return view('cms.pages.show', compact('page'));
    }

    public function edit(Post $page)
    {
        return redirect()->route('cms.posts.edit', $page);
    }

    public function update(Request $request, Post $page)
    {
        return redirect()->route('cms.posts.update', $page);
    }

    public function destroy(Post $page)
    {
        return redirect()->route('cms.posts.destroy', $page);
    }
}
