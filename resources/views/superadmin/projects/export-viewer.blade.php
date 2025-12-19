@extends('superadmin.layouts.app')

@section('title', 'Export Config - ' . $project->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.projects.config', $project) }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Quay lại Config
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Export Configuration</h1>
        <div class="flex gap-2">
            <button onclick="copyToClipboard()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Copy JSON
            </button>
            <a href="{{ route('superadmin.projects.export-config', $project) }}?format=download" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Download
            </a>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Project Info -->
        <div class="border rounded-lg p-4">
            <h3 class="font-semibold text-lg mb-3">Project Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-600">Name:</span>
                    <p class="font-medium">{{ $exportData['project']['name'] }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Code:</span>
                    <p class="font-medium">{{ $exportData['project']['code'] }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Status:</span>
                    <p class="font-medium">{{ $exportData['project']['status'] }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Created:</span>
                    <p class="font-medium">{{ $exportData['project']['created_at'] }}</p>
                </div>
            </div>
        </div>

        <!-- Debug Info -->
        <div class="border rounded-lg p-4 bg-yellow-50">
            <h3 class="font-semibold text-lg mb-3">Debug Information</h3>
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Export Time:</span>
                    <p class="font-medium">{{ $exportData['debug_info']['export_time'] }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Export By:</span>
                    <p class="font-medium">{{ $exportData['debug_info']['export_by'] }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Memory Usage:</span>
                    <p class="font-medium">{{ round($exportData['debug_info']['memory_usage'] / 1048576, 2) }} MB</p>
                </div>
                <div>
                    <span class="text-gray-600">Current File:</span>
                    <p class="font-medium text-xs">{{ $exportData['debug_info']['current_file']['relative_path'] }}</p>
                </div>
                <div>
                    <span class="text-gray-600">PHP Version:</span>
                    <p class="font-medium">{{ $exportData['debug_info']['php_version'] }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Laravel Version:</span>
                    <p class="font-medium">{{ $exportData['debug_info']['laravel_version'] }}</p>
                </div>
            </div>
        </div>

        <!-- File Analysis -->
        <div class="border rounded-lg p-4">
            <h3 class="font-semibold text-lg mb-3">File Analysis</h3>
            <div class="grid grid-cols-4 gap-4 mb-4">
                <div class="bg-blue-50 p-3 rounded">
                    <p class="text-sm text-gray-600">Total Files</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $exportData['file_analysis']['total_files'] }}</p>
                </div>
                <div class="bg-green-50 p-3 rounded">
                    <p class="text-sm text-gray-600">Recent Changes</p>
                    <p class="text-2xl font-bold text-green-600">{{ count($exportData['file_analysis']['recent_changes']) }}</p>
                </div>
                <div class="bg-orange-50 p-3 rounded">
                    <p class="text-sm text-gray-600">Large Files</p>
                    <p class="text-2xl font-bold text-orange-600">{{ count($exportData['file_analysis']['large_files']) }}</p>
                </div>
                <div class="bg-purple-50 p-3 rounded">
                    <p class="text-sm text-gray-600">File Types</p>
                    <p class="text-2xl font-bold text-purple-600">{{ count($exportData['file_analysis']['file_types']) }}</p>
                </div>
            </div>

            @if(count($exportData['file_analysis']['recent_changes']) > 0)
            <div class="mt-4">
                <h4 class="font-medium mb-2">Recent Changes (Last 24h)</h4>
                <div class="max-h-64 overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left">File</th>
                                <th class="px-3 py-2 text-left">Modified</th>
                                <th class="px-3 py-2 text-right">Size</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exportData['file_analysis']['recent_changes'] as $change)
                            <tr class="border-t">
                                <td class="px-3 py-2 font-mono text-xs">{{ $change['file'] }}</td>
                                <td class="px-3 py-2">{{ $change['modified'] }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($change['size']) }} bytes</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Eval Detection (if included) -->
        @if(isset($exportData['eval_detection']))
        <div class="border rounded-lg p-4 {{ $exportData['eval_detection']['found_eval'] ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
            <h3 class="font-semibold text-lg mb-3">Eval Detection</h3>
            
            @if($exportData['eval_detection']['found_eval'])
                <div class="mb-4">
                    <p class="text-red-800 font-medium">⚠️ Eval usage detected in {{ count($exportData['eval_detection']['eval_files']) }} file(s)</p>
                    <ul class="mt-2 space-y-1">
                        @foreach($exportData['eval_detection']['eval_files'] as $file)
                        <li class="text-sm text-red-700 font-mono">{{ $file }}</li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-green-800 font-medium">✓ No eval usage detected</p>
            @endif

            @if(count($exportData['eval_detection']['suspicious_functions']) > 0)
            <div class="mt-4">
                <h4 class="font-medium mb-2">Suspicious Functions Found</h4>
                <div class="max-h-64 overflow-y-auto">
                    @foreach($exportData['eval_detection']['suspicious_functions'] as $suspicious)
                    <div class="mb-3 p-2 bg-white rounded border">
                        <p class="text-sm font-medium">{{ $suspicious['file'] }}</p>
                        <p class="text-xs text-gray-600">Function: <code class="bg-gray-100 px-1 rounded">{{ $suspicious['function'] }}</code></p>
                        @foreach($suspicious['lines'] as $line)
                        <p class="text-xs font-mono mt-1">Line {{ $line['line_number'] }}: {{ $line['content'] }}</p>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Raw JSON -->
        <div class="border rounded-lg p-4">
            <h3 class="font-semibold text-lg mb-3">Raw JSON Data</h3>
            <pre id="json-data" class="bg-gray-900 text-green-400 p-4 rounded overflow-auto max-h-96 text-xs"><code>{{ json_encode($exportData, JSON_PRETTY_PRINT) }}</code></pre>
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    const jsonData = document.getElementById('json-data').textContent;
    navigator.clipboard.writeText(jsonData).then(() => {
        alert('JSON copied to clipboard!');
    });
}
</script>
@endsection