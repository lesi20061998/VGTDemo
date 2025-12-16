<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ZipArchive;

class TestExportController extends Controller
{
    public function testExport(Request $request, $projectCode)
    {
        try {
            $project = Project::where('code', $projectCode)->first();
            if (!$project) {
                return response()->json(['error' => 'Project not found'], 404);
            }
            
            // Create exports directory
            $exportsDir = storage_path('app/exports');
            if (!File::exists($exportsDir)) {
                File::makeDirectory($exportsDir, 0755, true);
            }
            
            // Create test export
            $exportPath = $exportsDir . '/' . $projectCode . '_test';
            if (File::exists($exportPath)) {
                File::deleteDirectory($exportPath);
            }
            File::makeDirectory($exportPath, 0755, true);
            
            // Create test files
            File::put($exportPath . '/test.txt', 'Test export for ' . $project->name);
            File::put($exportPath . '/data.json', json_encode([
                'project' => $project->toArray(),
                'exported_at' => now()->toISOString()
            ], JSON_PRETTY_PRINT));
            
            // Create ZIP
            $zipPath = $exportsDir . '/' . $projectCode . '_test.zip';
            $zip = new ZipArchive();
            
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($exportPath),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );
                
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($exportPath) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                
                $zip->close();
                
                // Clean up temp directory
                File::deleteDirectory($exportPath);
                
                return response()->download($zipPath)->deleteFileAfterSend();
            } else {
                throw new \Exception('Could not create ZIP file');
            }
            
        } catch (\Exception $e) {
            \Log::error('Test export failed: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}