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
        $postType = $request->query('type', 'post');
        $config = config("post_types.{$postType}");

        if (! $config) {
            abort(404, 'Loại nội dung không tồn tại.');
        }

        $posts = Post::with('author')
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->where('post_type', $postType)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('cms.posts.index', compact('posts', 'postType', 'config'));
    }

    public function create(Request $request)
    {
        $postType = $request->get('type', 'post');
        $config = config("post_types.{$postType}");

        if (! $config) {
            abort(404, 'Loại nội dung không tồn tại.');
        }

        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';
        $currentLang = $request->get('lang', $defaultLang);
        session(['admin_language' => $currentLang]);

        return view('cms.posts.create', compact('postType', 'config', 'currentLang'));
    }

    public function store(Request $request)
    {
        $postType = $request->input('post_type', 'post');
        $config = config("post_types.{$postType}");

        if (! $config) {
            abort(404, 'Loại nội dung không tồn tại.');
        }

        // Validate basic fields
        $rules = [
            'slug' => 'nullable|string|unique:posts,slug',
            'featured_image' => 'nullable|string',
            'post_type' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'translations' => 'required|array',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.excerpt' => 'nullable|string',
            'translations.*.content' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:60',
            'translations.*.meta_description' => 'nullable|string|max:160',
            'meta_data' => 'nullable|array',
        ];

        // Add dynamic validation from config
        if (isset($config['fields'])) {
            foreach ($config['fields'] as $key => $field) {
                // simple validation mapping (can be expanded)
                $rules["meta_data.{$key}"] = 'nullable';
            }
        }

        $validated = $request->validate($rules);

        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';

        $request->validate([
            "translations.{$defaultLang}.title" => 'required|string|max:255',
        ]);

        $defaultTitle = $request->input("translations.{$defaultLang}.title");
        $validated['slug'] = $validated['slug'] ?? Str::slug($defaultTitle);

        $validated['title'] = $defaultTitle;
        $validated['content'] = $request->input("translations.{$defaultLang}.content", '');
        $validated['excerpt'] = $request->input("translations.{$defaultLang}.excerpt", '');
        $validated['author_id'] = auth()->id();

        // Process meta data
        if ($request->has('meta_data')) {
            $validated['meta_data'] = $request->input('meta_data');
        }

        $post = Post::create($validated);

        if ($request->has('translations')) {
            $post->saveTranslations($request->input('translations'));
        }

        return redirect()->route('cms.posts.index', ['type' => $postType])->with('alert', [
            'type' => 'success',
            'message' => 'Thêm '.$config['name'].' thành công!',
        ]);
    }

    public function edit(Request $request, Post $post)
    {
        $postType = $post->post_type;
        $config = config("post_types.{$postType}");

        if (! $config) {
            abort(404, 'Loại nội dung không tồn tại.');
        }

        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';
        $currentLang = $request->get('lang', $defaultLang);
        session(['admin_language' => $currentLang]);

        return view('cms.posts.edit', compact('post', 'postType', 'config', 'currentLang'));
    }

    public function update(Request $request, Post $post)
    {
        $postType = $post->post_type;
        $config = config("post_types.{$postType}");

        $rules = [
            'slug' => 'nullable|string|unique:posts,slug,'.$post->id,
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'translations' => 'required|array',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.excerpt' => 'nullable|string',
            'translations.*.content' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:60',
            'translations.*.meta_description' => 'nullable|string|max:160',
            'meta_data' => 'nullable|array',
        ];

        $validated = $request->validate($rules);

        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';

        $request->validate([
            "translations.{$defaultLang}.title" => 'required|string|max:255',
        ]);

        $defaultTitle = $request->input("translations.{$defaultLang}.title");
        $validated['slug'] = $validated['slug'] ?? Str::slug($defaultTitle);

        $validated['title'] = $defaultTitle;
        $validated['content'] = $request->input("translations.{$defaultLang}.content", '');
        $validated['excerpt'] = $request->input("translations.{$defaultLang}.excerpt", '');

        if ($request->has('meta_data')) {
            $validated['meta_data'] = $request->input('meta_data');
        }

        $post->update($validated);

        if ($request->has('translations')) {
            $post->saveTranslations($request->input('translations'));
        }

        return redirect()->route('cms.posts.edit', $post)->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật '.$config['name'].' thành công!',
        ]);
    }

    public function destroy(Post $post)
    {
        $postType = $post->post_type;
        $config = config("post_types.{$postType}");

        $post->delete();

        return redirect()->route('cms.posts.index', ['type' => $postType])->with('alert', [
            'type' => 'success',
            'message' => 'Xóa '.($config['name'] ?? 'dữ liệu').' thành công!',
        ]);
    }
}
