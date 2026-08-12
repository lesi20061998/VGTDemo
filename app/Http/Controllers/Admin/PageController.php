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

    public function create(Request $request, $projectCode = null)
    {
        $code = $projectCode ?? request()->route('projectCode');
        if ($code) {
            return redirect()->route('project.admin.posts.create', ['projectCode' => $code, 'type' => 'page']);
        }

        return redirect()->route('cms.posts.create', ['type' => 'page']);
    }

    public function store(Request $request)
    {
        // Handled by PostController via redirect
        return redirect()->back();
    }

    public function show(Post $page, $projectCode = null)
    {
        return view('cms.pages.show', compact('page'));
    }

    public function edit($projectCodeOrPost, $postId = null)
    {
        $code = request()->route('projectCode');
        $post = $postId ?? $projectCodeOrPost;
        if ($code) {
            return redirect()->route('project.admin.pages.edit', ['projectCode' => $code, 'post' => $post]);
        }

        return redirect()->route('cms.posts.edit', $post);
    }

    public function update(Request $request, Post $page)
    {
        return redirect()->back();
    }

    public function destroy($projectCodeOrPost, $postId = null)
    {
        $code = request()->route('projectCode');
        $post = $postId ?? $projectCodeOrPost;
        $pageModel = is_numeric($post) ? Post::findOrFail($post) : Post::where('slug', $post)->firstOrFail();
        $pageModel->delete();

        if ($code) {
            return redirect()->route('project.admin.pages.index', $code)->with('alert', [
                'type' => 'success',
                'message' => 'Đã xóa trang thành công.',
            ]);
        }

        return redirect()->route('cms.pages.index')->with('alert', [
            'type' => 'success',
            'message' => 'Đã xóa trang thành công.',
        ]);
    }
}
