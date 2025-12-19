<!DOCTYPE html>
<html>
<head>
    <title>Test Logging</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-xl font-bold mb-4">Test File Changes Logging</h1>
        
        <form id="test-form" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2">Test Data:</label>
                <input type="text" name="test_field" class="w-full px-3 py-2 border rounded-md" placeholder="Enter test data">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Description:</label>
                <textarea name="description" class="w-full px-3 py-2 border rounded-md" rows="3" placeholder="Test description"></textarea>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
                Submit Test
            </button>
        </form>
        
        <div id="result" class="mt-4 hidden">
            <div class="p-3 bg-green-100 border border-green-300 rounded-md">
                <p class="text-green-800">Test submitted successfully! Check the log file.</p>
            </div>
        </div>
        
        <div class="mt-6 text-sm text-gray-600">
            <p><strong>Log file location:</strong> storage/logs/file-changes-main.log</p>
            <p><strong>View logs:</strong> <a href="/superadmin/file-monitor?project=main" class="text-blue-600 hover:underline">File Monitor</a></p>
        </div>
    </div>

    <script>
        document.getElementById('test-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('/superadmin/test-log', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                console.log('Response:', result);
                
                document.getElementById('result').classList.remove('hidden');
                
                // Reset form
                this.reset();
                
            } catch (error) {
                console.error('Error:', error);
                alert('Error submitting test');
            }
        });
    </script>
</body>
</html>