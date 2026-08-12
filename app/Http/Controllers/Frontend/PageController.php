<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
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

    public function contactSubmit(Request $request, $projectCode = null, $locale = null)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'service' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'message.required' => 'Vui lòng nhập nội dung.',
        ]);

        FormSubmission::create([
            'form_name' => 'contact',
            'data' => [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'service' => $validated['service'] ?? null,
                'message' => $validated['message'],
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'pending',
            'tenant_id' => session('current_tenant_id'),
        ]);

        return back()->with('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.');
    }
}
