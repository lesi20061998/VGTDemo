@extends('superadmin.layouts.app')

@section('title', 'File Change Monitor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title d-flex align-items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v0H8v0z"></path>
                        </svg>
                        File Change Monitor
                    </h3>
                    <div>
                        <select class="form-control d-inline-block" style="width: auto;" onchange="changeProject(this.value)">
                            @foreach($projects as $project)
                                <option value="{{ $project->code }}" {{ $projectCode === $project->code ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary ml-2 d-flex align-items-center" onclick="refreshLogs()">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Recent File Changes -->
                    <div class="mb-4">
                        <h5 class="d-flex align-items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Recent File Changes (24h)
                        </h5>
                        <div id="recent-changes" class="table-responsive">
                            <div class="text-center">
                                <div class="spinner-border" role="status"></div>
                                <p>Loading recent changes...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activity Logs -->
                    <div>
                        <h5 class="d-flex align-items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Activity Logs
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Route</th>
                                        <th>IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($log->timestamp)->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $log->user_name }}</span>
                                            <small class="text-muted">(ID: {{ $log->user_id }})</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $log->method === 'POST' ? 'success' : ($log->method === 'DELETE' ? 'danger' : 'warning') }}">
                                                {{ $log->method }}
                                            </span>
                                        </td>
                                        <td>
                                            <code>{{ $log->route ?? 'N/A' }}</code>
                                            <br><small class="text-muted">{{ Str::limit($log->url, 50) }}</small>
                                        </td>
                                        <td>{{ $log->ip_address }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No activity logs found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshLogs() {
    location.reload();
}

function changeProject(projectCode) {
    window.location.href = '/superadmin/file-monitor?project=' + projectCode;
}

// Load recent file changes
fetch('/superadmin/file-monitor/recent-changes')
    .then(response => response.json())
    .then(data => {
        let html = '<table class="table table-sm"><thead><tr><th>File</th><th>Modified</th><th>Size</th></tr></thead><tbody>';
        
        if (data.length === 0) {
            html += '<tr><td colspan="3" class="text-center text-muted">No recent changes</td></tr>';
        } else {
            data.forEach(file => {
                html += `<tr>
                    <td><code>${file.file}</code></td>
                    <td>${file.modified}</td>
                    <td>${(file.size / 1024).toFixed(1)} KB</td>
                </tr>`;
            });
        }
        
        html += '</tbody></table>';
        document.getElementById('recent-changes').innerHTML = html;
    })
    .catch(error => {
        document.getElementById('recent-changes').innerHTML = '<div class="alert alert-danger">Error loading file changes</div>';
    });
</script>
@endsection