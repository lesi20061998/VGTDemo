<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Get media storage path based on project or tenant
     */
    private function getMediaPath(?Request $request = null)
    {
        // Check for project code from route parameter (multi-site)
        $projectCode = $request?->route('projectCode');
        if ($projectCode) {
            return "media/project-{$projectCode}";
        }

        // Fallback to tenant_id for backward compatibility
        $tenant = auth()->user()->tenant_id ?? session('tenant_id');
        if ($tenant) {
            return "media/tenant-{$tenant}";
        }

        return 'media';
    }

    /**
     * @deprecated Use getMediaPath() instead
     */
    private function getTenantPath()
    {
        return $this->getMediaPath(request());
    }

    public function list(Request $request)
    {
        $basePath = $this->getMediaPath($request);
        $path = $request->get('path', '');
        $fullPath = $path ? $basePath.'/'.ltrim($path, '/') : $basePath;

        // Ensure base directory exists
        if (! Storage::disk('public')->exists($basePath)) {
            Storage::disk('public')->makeDirectory($basePath);
        }

        // Get folders
        $directories = Storage::disk('public')->directories($fullPath);
        $folders = collect($directories)->map(function ($dir) use ($basePath) {
            return [
                'name' => basename($dir),
                'path' => str_replace($basePath.'/', '', $dir),
            ];
        })->values();

        // Get files
        $files = Storage::disk('public')->files($fullPath);
        $media = collect($files)->filter(function ($file) {
            return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'mov', 'avi', 'mkv']);
        })->map(function ($file) {
            return [
                'id' => md5($file),
                'name' => basename($file),
                'url' => Storage::url($file),
                'path' => $file,
            ];
        })->values();

        return response()->json([
            'folders' => $folders,
            'files' => $media,
        ]);
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'files' => 'required|array',
                'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,webp,svg,mp4,webm,mov,avi,mkv|max:1048576',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all()),
                'errors' => $e->validator->errors()->toArray()
            ], 422);
        }

        $basePath = $this->getMediaPath($request);
        $path = $request->get('path', '');
        $fullPath = $path ? $basePath.'/'.ltrim($path, '/') : $basePath;

        // Ensure directory exists
        if (! Storage::disk('public')->exists($fullPath)) {
            Storage::disk('public')->makeDirectory($fullPath);
        }

        $uploaded = [];
        $files = $request->file('files');
        
        if (empty($files)) {
            return response()->json([
                'success' => false,
                'message' => 'No files provided'
            ], 400);
        }
        
        foreach ($files as $file) {
            try {
                $filename = time().'_'.$file->getClientOriginalName();
                $filePath = $file->storeAs($fullPath, $filename, 'public');

                $uploaded[] = [
                    'id' => md5($filePath),
                    'name' => basename($filePath),
                    'url' => Storage::url($filePath),
                    'path' => $filePath,
                ];
            } catch (\Exception $e) {
                \Log::error('File upload error: ' . $e->getMessage());
            }
        }

        if (empty($uploaded)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload files'
            ], 500);
        }

        return response()->json(['success' => true, 'files' => $uploaded]);
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'path' => 'nullable|string',
        ]);

        $basePath = $this->getMediaPath($request);
        $path = $request->get('path', '');
        $fullPath = $path ? $basePath.'/'.ltrim($path, '/').'/'.$request->name : $basePath.'/'.$request->name;

        try {
            Storage::disk('public')->makeDirectory($fullPath);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function deleteFolder(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $basePath = $this->getMediaPath($request);
        $fullPath = $basePath.'/'.ltrim($request->path, '/');
        Storage::disk('public')->deleteDirectory($fullPath);

        return response()->json(['success' => true]);
    }

    public function move(Request $request)
    {
        $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
            'type' => 'required|in:file,folder',
        ]);

        $basePath = $this->getMediaPath($request);
        $fromPath = $request->type === 'folder' ? $basePath.'/'.ltrim($request->from, '/') : $basePath.'/'.ltrim($request->from, '/');
        $toPath = $basePath.'/'.ltrim($request->to, '/').'/'.basename($fromPath);

        if ($request->type === 'folder') {
            if (Storage::disk('public')->exists($fromPath)) {
                Storage::disk('public')->move($fromPath, $toPath);

                return response()->json(['success' => true]);
            }
        } else {
            if (Storage::disk('public')->exists($fromPath)) {
                Storage::disk('public')->move($fromPath, $toPath);

                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false], 404);
    }

    public function destroy(Request $request, $id)
    {
        $basePath = $this->getMediaPath($request);
        $files = Storage::disk('public')->allFiles($basePath);
        $file = collect($files)->first(fn ($f) => md5($f) === $id);

        if ($file) {
            Storage::disk('public')->delete($file);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
