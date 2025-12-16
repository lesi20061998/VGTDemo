<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FontController extends Controller
{
    public function index()
    {
        $fonts = setting('fonts', []);
        return view('cms.settings.fonts', compact('fonts'));
    }

    public function getGoogleFonts(Request $request)
    {
        $response = Http::get("https://www.googleapis.com/webfonts/v1/webfonts", [
            'key' => 'AIzaSyC5t_7sZdp8KqF0HqYzKjHqYqZqYqZqYqY',
            'sort' => 'popularity'
        ]);

        if ($response->successful()) {
            $fonts = collect($response->json()['items'] ?? [])->take(100);
            return response()->json($fonts->values());
        }

        // Fallback data
        return response()->json([
            ['family' => 'Roboto'],
            ['family' => 'Open Sans'],
            ['family' => 'Lato'],
            ['family' => 'Montserrat'],
            ['family' => 'Poppins'],
            ['family' => 'Raleway'],
            ['family' => 'Ubuntu'],
            ['family' => 'Nunito'],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required',
            'type' => 'required',
            'label' => 'required',
            'load' => 'required',
        ]);

        $fonts = setting('fonts', []);
        
        $fonts[] = [
            'id' => uniqid(),
            'key' => $request->key,
            'type' => $request->type,
            'label' => $request->label,
            'load' => $request->load,
            'is_active' => true,
            'is_default' => false,
        ];

        setting(['fonts' => $fonts]);

        return back()->with('success', 'Đã thêm font');
    }

    public function toggle(Request $request)
    {
        $fonts = setting('fonts', []);
        $fonts = collect($fonts)->map(function($font) use ($request) {
            if ($font['id'] === $request->id) {
                $font['is_active'] = !$font['is_active'];
            }
            return $font;
        })->toArray();

        setting(['fonts' => $fonts]);
        return back()->with('success', 'Đã cập nhật');
    }

    public function setDefault(Request $request)
    {
        $fonts = setting('fonts', []);
        $fonts = collect($fonts)->map(function($font) use ($request) {
            $font['is_default'] = $font['id'] === $request->id;
            return $font;
        })->toArray();

        setting(['fonts' => $fonts]);
        return back()->with('success', 'Đã đặt mặc định. Chạy: npm run build')->with('warning', 'Cần rebuild CSS!');
    }

    public function destroy(Request $request)
    {
        $fonts = setting('fonts', []);
        $fonts = collect($fonts)->reject(fn($font) => $font['id'] === $request->id)->values()->toArray();

        setting(['fonts' => $fonts]);
        return back()->with('success', 'Đã xóa');
    }
}

