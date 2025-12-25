<?php

// Test Media Upload in Local Environment
echo "=== Testing Media Upload (Local) ===\n\n";

try {
    require_once 'vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Checking Authentication...\n";
    
    // Find admin user and authenticate
    $adminUser = DB::table('users')
        ->where('role', 'admin')
        ->where('level', 0)
        ->first();
        
    if ($adminUser) {
        Auth::loginUsingId($adminUser->id);
        echo "   ✓ Authenticated as: " . ($adminUser->username ?: 'admin') . "\n";
    } else {
        echo "   ✗ No admin user found\n";
        exit;
    }
    
    echo "\n2. Testing Storage Configuration...\n";
    
    $storageConfig = config('filesystems.disks.public');
    echo "   Driver: " . $storageConfig['driver'] . "\n";
    echo "   Root: " . $storageConfig['root'] . "\n";
    echo "   URL: " . $storageConfig['url'] . "\n";
    
    // Check if storage directories exist
    $storagePath = storage_path('app/public');
    $publicPath = public_path('storage');
    
    echo "   Storage path exists: " . (is_dir($storagePath) ? '✓' : '✗') . " {$storagePath}\n";
    echo "   Public symlink exists: " . (is_dir($publicPath) ? '✓' : '✗') . " {$publicPath}\n";
    
    echo "\n3. Testing Media Directory Creation...\n";
    
    $mediaPath = 'media/test-local';
    
    if (!Storage::disk('public')->exists($mediaPath)) {
        Storage::disk('public')->makeDirectory($mediaPath);
        echo "   ✓ Created media directory: {$mediaPath}\n";
    } else {
        echo "   ✓ Media directory exists: {$mediaPath}\n";
    }
    
    echo "\n4. Testing File Upload Simulation...\n";
    
    // Create a test file
    $testContent = "Test file content - " . date('Y-m-d H:i:s');
    $testFileName = 'test_' . time() . '.txt';
    $testFilePath = $mediaPath . '/' . $testFileName;
    
    Storage::disk('public')->put($testFilePath, $testContent);
    
    if (Storage::disk('public')->exists($testFilePath)) {
        echo "   ✓ Test file created: {$testFilePath}\n";
        echo "   ✓ File URL: " . Storage::url($testFilePath) . "\n";
        
        // Clean up test file
        Storage::disk('public')->delete($testFilePath);
        echo "   ✓ Test file cleaned up\n";
    } else {
        echo "   ✗ Failed to create test file\n";
    }
    
    echo "\n5. Testing Media Controller Routes...\n";
    
    // Test media list endpoint
    $request = new \Illuminate\Http\Request();
    $request->setMethod('GET');
    
    $mediaController = new \App\Http\Controllers\Admin\MediaController();
    
    try {
        $response = $mediaController->list($request);
        $data = json_decode($response->getContent(), true);
        
        echo "   ✓ Media list endpoint working\n";
        echo "   ✓ Found " . count($data['folders']) . " folders\n";
        echo "   ✓ Found " . count($data['files']) . " files\n";
        
    } catch (Exception $e) {
        echo "   ✗ Media list error: " . $e->getMessage() . "\n";
    }
    
    echo "\n6. Testing CSRF Token...\n";
    
    $csrfToken = csrf_token();
    echo "   CSRF Token: " . ($csrfToken ? '✓ Generated' : '✗ Empty') . "\n";
    echo "   Token (first 10 chars): " . substr($csrfToken, 0, 10) . "...\n";
    
    echo "\n7. Creating Local Media Test Page...\n";
    
    $testPage = '<!DOCTYPE html>
<html>
<head>
    <title>Local Media Upload Test</title>
    <meta name="csrf-token" content="' . csrf_token() . '">
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .upload-area { border: 2px dashed #ccc; padding: 40px; text-align: center; margin: 20px 0; }
        .upload-area:hover { border-color: #007cba; background: #f9f9f9; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, button { padding: 10px; margin: 5px; }
        button { background: #007cba; color: white; border: none; cursor: pointer; }
        .result { margin-top: 20px; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .media-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-top: 20px; }
        .media-item { border: 1px solid #ddd; padding: 10px; border-radius: 4px; }
        .media-item img { max-width: 100%; height: auto; }
    </style>
</head>
<body>
    <h1>Local Media Upload Test</h1>
    
    <div class="form-group">
        <h3>Current Status:</h3>
        <p><strong>User:</strong> ' . (auth()->user()->username ?? 'Not logged in') . '</p>
        <p><strong>Environment:</strong> ' . app()->environment() . '</p>
        <p><strong>CSRF Token:</strong> ' . substr(csrf_token(), 0, 20) . '...</p>
    </div>
    
    <div class="form-group">
        <h3>Create Folder:</h3>
        <input type="text" id="folderName" placeholder="Folder name" value="test-folder-' . time() . '">
        <button onclick="createFolder()">Create Folder</button>
    </div>
    
    <div class="form-group">
        <h3>Upload Files:</h3>
        <div class="upload-area" onclick="document.getElementById(\'fileInput\').click()">
            <p>Click here to select files or drag and drop</p>
            <input type="file" id="fileInput" multiple accept="image/*,video/*" style="display: none;" onchange="uploadFiles()">
        </div>
    </div>
    
    <div id="result"></div>
    
    <div class="form-group">
        <h3>Current Media:</h3>
        <button onclick="loadMedia()">Refresh Media List</button>
        <div id="mediaList" class="media-list"></div>
    </div>
    
    <script>
        const csrfToken = document.querySelector(\'meta[name="csrf-token"]\').getAttribute(\'content\');
        
        function showResult(message, type = \'success\') {
            const result = document.getElementById(\'result\');
            result.innerHTML = `<div class="result ${type}">${message}</div>`;
        }
        
        async function createFolder() {
            const folderName = document.getElementById(\'folderName\').value;
            if (!folderName) {
                showResult(\'Please enter a folder name\', \'error\');
                return;
            }
            
            try {
                const response = await fetch(\'/admin/media/folder\', {
                    method: \'POST\',
                    headers: {
                        \'Content-Type\': \'application/json\',
                        \'X-CSRF-TOKEN\': csrfToken,
                        \'Accept\': \'application/json\'
                    },
                    body: JSON.stringify({
                        name: folderName,
                        path: \'\'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showResult(`Folder "${folderName}" created successfully!`);
                    loadMedia();
                } else {
                    showResult(`Error: ${data.message || \'Unknown error\'}`, \'error\');
                }
            } catch (error) {
                showResult(`Network error: ${error.message}`, \'error\');
            }
        }
        
        async function uploadFiles() {
            const fileInput = document.getElementById(\'fileInput\');
            const files = fileInput.files;
            
            if (files.length === 0) {
                showResult(\'Please select files to upload\', \'error\');
                return;
            }
            
            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append(\'files[]\', files[i]);
            }
            formData.append(\'path\', \'\');
            
            try {
                showResult(\'Uploading files...\', \'success\');
                
                const response = await fetch(\'/admin/media/upload\', {
                    method: \'POST\',
                    headers: {
                        \'X-CSRF-TOKEN\': csrfToken,
                        \'Accept\': \'application/json\'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showResult(`Successfully uploaded ${data.files.length} file(s)!`);
                    loadMedia();
                    fileInput.value = \'\'; // Clear input
                } else {
                    showResult(`Upload error: ${data.message || \'Unknown error\'}`, \'error\');
                }
            } catch (error) {
                showResult(`Upload failed: ${error.message}`, \'error\');
            }
        }
        
        async function loadMedia() {
            try {
                const response = await fetch(\'/admin/media/list\', {
                    headers: {
                        \'Accept\': \'application/json\',
                        \'X-CSRF-TOKEN\': csrfToken
                    }
                });
                
                const data = await response.json();
                const mediaList = document.getElementById(\'mediaList\');
                
                let html = \'\';
                
                // Show folders
                data.folders.forEach(folder => {
                    html += `<div class="media-item">
                        <strong>📁 ${folder.name}</strong>
                        <br><small>Folder</small>
                    </div>`;
                });
                
                // Show files
                data.files.forEach(file => {
                    const isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(file.name);
                    html += `<div class="media-item">
                        ${isImage ? `<img src="${file.url}" alt="${file.name}">` : `<p>📄 ${file.name}</p>`}
                        <br><small>${file.name}</small>
                    </div>`;
                });
                
                if (html === \'\') {
                    html = \'<p>No media files found. Upload some files to get started!</p>\';
                }
                
                mediaList.innerHTML = html;
                
            } catch (error) {
                showResult(`Failed to load media: ${error.message}`, \'error\');
            }
        }
        
        // Load media on page load
        loadMedia();
    </script>
</body>
</html>';

    file_put_contents('test_media_local.html', $testPage);
    echo "   ✓ Created test_media_local.html\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Local Media Test Complete ===\n";
echo "\nTEST YOUR MEDIA UPLOAD:\n";
echo "1. Make sure server is running: php artisan serve\n";
echo "2. Visit: http://localhost:8000/test_media_local.html\n";
echo "3. Try creating folders and uploading files\n";
echo "4. Check if files appear in storage/app/public/media/\n";
echo "5. Verify URLs work correctly\n";
echo "\nIf upload fails, check:\n";
echo "- CSRF token is being sent\n";
echo "- File permissions on storage directory\n";
echo "- Laravel logs in storage/logs/\n";