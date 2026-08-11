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

    private function resolvePost($projectCodeOrPost, $postId = null): Post
    {
        if ($projectCodeOrPost instanceof Post) {
            return $projectCodeOrPost;
        }

        if ($postId instanceof Post) {
            return $postId;
        }

        $param = $postId ?? $projectCodeOrPost;

        if (is_numeric($param)) {
            return Post::where('id', $param)->firstOrFail();
        }

        return Post::where('slug', $param)->orWhere('id', $param)->firstOrFail();
    }

    public function index(Request $request, $projectCode = null)
    {
        $postType = $request->query('type', 'post');
        $config = config("post_types.{$postType}");

        if (! $config) {
            $config = [
                'name' => ucfirst($postType),
                'singular_name' => ucfirst($postType),
            ];
        }

        $posts = Post::with('author')
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->where('post_type', $postType)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('cms.posts.index', compact('posts', 'postType', 'config'));
    }

    public function create(Request $request, $projectCode = null)
    {
        $postType = $request->get('type', 'post');
        $config = config("post_types.{$postType}");

        if (! $config) {
            $config = [
                'name' => ucfirst($postType),
                'singular_name' => ucfirst($postType),
            ];
        }

        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';
        $currentLang = $request->get('lang', $defaultLang);
        session(['admin_language' => $currentLang]);

        return view('cms.posts.create', compact('postType', 'config', 'currentLang'));
    }

    public function store(Request $request, $projectCode = null)
    {
        $postType = $request->input('post_type', 'post');
        $config = config("post_types.{$postType}");

        if (! $config) {
            $config = [
                'name' => ucfirst($postType),
                'singular_name' => ucfirst($postType),
            ];
        }

        // Validate basic fields
        $rules = [
            'slug' => 'nullable|string|unique:posts,slug',
            'featured_image' => 'nullable|string',
            'post_type' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'translations' => 'nullable|array',
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
                $rules["meta_data.{$key}"] = 'nullable';
            }
        }

        $validated = $request->validate($rules);

        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';

        $defaultTitle = $request->input("translations.{$defaultLang}.title") ?? $request->input('title');
        if (empty($defaultTitle)) {
            $defaultTitle = $request->input('name') ?? 'Bài viết mới';
        }

        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($defaultTitle);
        $validated['title'] = $defaultTitle;
        $validated['content'] = $request->input("translations.{$defaultLang}.content", $request->input('content', ''));
        $validated['excerpt'] = $request->input("translations.{$defaultLang}.excerpt", $request->input('excerpt', ''));
        $validated['author_id'] = auth()->id();

        // Process meta data
        if ($request->has('meta_data')) {
            $validated['meta_data'] = $request->input('meta_data');
        }

        $post = Post::create($validated);

        if ($request->has('translations')) {
            $post->saveTranslations($request->input('translations'));
        }

        $projectCode = request()->route('projectCode');
        $route = $projectCode
            ? route('project.admin.posts.index', ['projectCode' => $projectCode, 'type' => $postType])
            : route('cms.posts.index', ['type' => $postType]);

        return redirect($route)->with('alert', [
            'type' => 'success',
            'message' => 'Thêm '.($config['name'] ?? 'bài viết').' thành công!',
        ]);
    }

    public function show(Request $request, $projectCodeOrPost = null, $postId = null)
    {
        $post = $this->resolvePost($projectCodeOrPost, $postId);

        return view('cms.posts.show', compact('post'));
    }

    public function edit(Request $request, $projectCodeOrPost = null, $postId = null)
    {
        $post = $this->resolvePost($projectCodeOrPost, $postId);
        $postType = $post->post_type;
        $config = config("post_types.{$postType}");

        if (! $config) {
            $config = [
                'name' => ucfirst($postType),
                'singular_name' => ucfirst($postType),
            ];
        }

        $languages = setting('languages', []);
        $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';
        $currentLang = $request->get('lang', $defaultLang);
        session(['admin_language' => $currentLang]);

        return view('cms.posts.edit', compact('post', 'postType', 'config', 'currentLang'));
    }

    public function update(Request $request, $projectCodeOrPost = null, $postId = null)
    {
        $post = $this->resolvePost($projectCodeOrPost, $postId);
        $postType = $post->post_type;
        $config = config("post_types.{$postType}");

        if (! $config) {
            $config = [
                'name' => ucfirst($postType),
                'singular_name' => ucfirst($postType),
            ];
        }

        $rules = [
            'slug' => 'nullable|string|unique:posts,slug,'.$post->id,
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'translations' => 'nullable|array',
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

        $defaultTitle = $request->input("translations.{$defaultLang}.title") ?? $request->input('title', $post->title);
        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : ($post->slug ?: Str::slug($defaultTitle));

        $validated['title'] = $defaultTitle;
        $validated['content'] = $request->input("translations.{$defaultLang}.content", $request->input('content', $post->content));
        $validated['excerpt'] = $request->input("translations.{$defaultLang}.excerpt", $request->input('excerpt', $post->excerpt));

        if ($request->has('meta_data')) {
            $validated['meta_data'] = $request->input('meta_data');
        }

        $post->update($validated);

        if ($request->has('translations')) {
            $post->saveTranslations($request->input('translations'));
        }

        $projectCode = request()->route('projectCode');
        $route = $projectCode
            ? route('project.admin.posts.edit', ['projectCode' => $projectCode, 'post' => $post->slug ?: $post->id])
            : route('cms.posts.edit', $post->slug ?: $post->id);

        return redirect($route)->with('alert', [
            'type' => 'success',
            'message' => 'Cập nhật '.($config['name'] ?? 'bài viết').' thành công!',
        ]);
    }

    public function destroy(Request $request, $projectCodeOrPost = null, $postId = null)
    {
        $post = $this->resolvePost($projectCodeOrPost, $postId);
        $postType = $post->post_type;
        $config = config("post_types.{$postType}");

        $post->delete();

        $projectCode = request()->route('projectCode');
        $route = $projectCode
            ? route('project.admin.posts.index', ['projectCode' => $projectCode, 'type' => $postType])
            : route('cms.posts.index', ['type' => $postType]);

        return redirect($route)->with('alert', [
            'type' => 'success',
            'message' => 'Xóa '.($config['name'] ?? 'dữ liệu').' thành công!',
        ]);
    }
}
