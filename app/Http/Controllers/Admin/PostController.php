<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Traits\HasAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use HasAlerts;

    public function index(Request $request)
    {
        $posts = Post::with('author')
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->post_type, fn ($q) => $q->where('post_type', $request->post_type))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('cms.posts.index', compact('posts'));
    }

    public function create(Request $request)
    {
        $postType = $request->get('type', 'post'); // post hoặc page

        // Lấy ngôn ngữ hiện tại từ URL parameter
        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';
        $currentLang = $request->get('lang', $defaultLang);

        // Lưu ngôn ngữ hiện tại vào session
        session(['admin_language' => $currentLang]);

        return view('cms.posts.create', compact('postType', 'currentLang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'nullable|string|unique:posts,slug',
            'featured_image' => 'nullable|string',
            'post_type' => 'required|in:post,page',
            'template' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'translations' => 'required|array',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.excerpt' => 'nullable|string',
            'translations.*.content' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:60',
            'translations.*.meta_description' => 'nullable|string|max:160',
        ]);

        // Lấy ngôn ngữ mặc định
        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';

        // Validate ngôn ngữ mặc định phải có tiêu đề và nội dung
        $request->validate([
            "translations.{$defaultLang}.title" => 'required|string|max:255',
            "translations.{$defaultLang}.content" => 'required|string',
        ]);

        // Tạo slug từ tiêu đề ngôn ngữ mặc định
        $defaultTitle = $request->input("translations.{$defaultLang}.title");
        $validated['slug'] = $validated['slug'] ?? Str::slug($defaultTitle);

        // Tạo post với thông tin cơ bản
        $validated['title'] = $defaultTitle; // Tạm thời để pass validation
        $validated['content'] = $request->input("translations.{$defaultLang}.content");
        $validated['excerpt'] = $request->input("translations.{$defaultLang}.excerpt");
        $validated['author_id'] = auth()->id();

        $post = Post::create($validated);

        // Lưu translations
        if ($request->has('translations')) {
            $post->saveTranslations($request->input('translations'));
        }

        $routeName = $post->post_type === 'page' ? 'cms.pages.index' : 'cms.posts.index';

        return redirect()->route($routeName)->with('alert', [
            'type' => 'success',
            'message' => $post->post_type === 'page' ? 'Thêm trang thành công!' : 'Thêm bài viết thành công!',
        ]);
    }

    public function show(Post $post)
    {
        return view('cms.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view('cms.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'slug' => 'nullable|string|unique:posts,slug,'.$post->id,
            'featured_image' => 'nullable|string',
            'post_type' => 'required|in:post,page',
            'template' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'translations' => 'required|array',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.excerpt' => 'nullable|string',
            'translations.*.content' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:60',
            'translations.*.meta_description' => 'nullable|string|max:160',
        ]);

        // Lấy ngôn ngữ mặc định
        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';

        // Validate ngôn ngữ mặc định phải có tiêu đề và nội dung
        $request->validate([
            "translations.{$defaultLang}.title" => 'required|string|max:255',
            "translations.{$defaultLang}.content" => 'required|string',
        ]);

        // Tạo slug từ tiêu đề ngôn ngữ mặc định nếu chưa có
        $defaultTitle = $request->input("translations.{$defaultLang}.title");
        $validated['slug'] = $validated['slug'] ?? Str::slug($defaultTitle);

        // Cập nhật thông tin cơ bản (tạm thời để pass validation)
        $validated['title'] = $defaultTitle;
        $validated['content'] = $request->input("translations.{$defaultLang}.content");
        $validated['excerpt'] = $request->input("translations.{$defaultLang}.excerpt");

        $post->update($validated);

        // Lưu translations
        if ($request->has('translations')) {
            $post->saveTranslations($request->input('translations'));
        }

        return redirect()->route('cms.posts.edit', $post)->with('alert', [
            'type' => 'success',
            'message' => $post->post_type === 'page' ? 'Cập nhật trang thành công!' : 'Cập nhật bài viết thành công!',
        ]);
    }

    public function destroy(Post $post)
    {
        $postType = $post->post_type;
        $post->delete();

        $routeName = $postType === 'page' ? 'cms.pages.index' : 'cms.posts.index';

        return redirect()->route($routeName)->with('alert', [
            'type' => 'success',
            'message' => $postType === 'page' ? 'Xóa trang thành công!' : 'Xóa bài viết thành công!',
        ]);
    }
}
