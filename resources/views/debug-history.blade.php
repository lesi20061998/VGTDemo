<!DOCTYPE html>
<html>
<head>
    <title>Debug History</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Debug File Monitor API</h1>
        
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Test API Endpoints</h2>
            
            <div class="space-y-4">
                <div>
                    <button onclick="testFileMonitor()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Test File Monitor API
                    </button>
                </div>
                
                <div>
                    <button onclick="testDebugRoute()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Test Debug Route
                    </button>
                </div>
                
                <div>
                    <button onclick="checkLogFile()" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                        Check Log File Content
                    </button>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Results</h2>
            <div id="results" class="space-y-4">
                <p class="text-gray-500">Click buttons above to test...</p>
            </div>
        </div>
    </div>

    <script>
        function addResult(title, data, isError = false) {
            const results = document.getElementById('results');
            const div = document.createElement('div');
            div.className = `p-4 rounded-lg border ${isError ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200'}`;
            div.innerHTML = `
                <h3 class="font-semibold ${isError ? 'text-red-800' : 'text-green-800'} mb-2">${title}</h3>
                <pre class="text-sm ${isError ? 'text-red-700' : 'text-green-700'} overflow-auto">${JSON.stringify(data, null, 2)}</pre>
            `;
            results.appendChild(div);
        }

        async function testFileMonitor() {
            try {
                const response = await fetch('/superadmin/file-monitor?project=main', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                addResult('File Monitor API Response', {
                    status: response.status,
                    data: data
                });
            } catch (error) {
                addResult('File Monitor API Error', {
                    error: error.message,
                    stack: error.stack
                }, true);
            }
        }

        async function testDebugRoute() {
            try {
                const response = await fetch('/superadmin/debug-file-monitor?project=main');
                const data = await response.json();
                addResult('Debug Route Response', {
                    status: response.status,
                    data: data
                });
            } catch (error) {
                addResult('Debug Route Error', {
                    error: error.message,
                    stack: error.stack
                }, true);
            }
        }

        async function checkLogFile() {
            try {
                // This will show what we expect to see
                const logContent = `{"timestamp":"2025-12-19T10:25:09.015532Z","user_id":1,"user_name":"Admin","user_email":"admin@example.com","method":"POST","url":"http://localhost:8000/superadmin/test-log","route":"superadmin.test-log","ip":"127.0.0.1","user_agent":"Mozilla/5.0","action":"Test-log Superadmin","data_summary":{"test_field":"111","description":"1111"},"project_code":"main"}`;
                
                const parsed = JSON.parse(logContent);
                addResult('Expected Log File Content', {
                    raw: logContent,
                    parsed: parsed
                });
            } catch (error) {
                addResult('Log File Parse Error', {
                    error: error.message
                }, true);
            }
        }
    </script>
</body>
</html>