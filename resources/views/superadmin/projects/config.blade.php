@extends('superadmin.layouts.app')

@section('title', 'Cấu hình Project')
@section('page-title', 'Cấu hình chức năng - ' . $project->name)

@section('content')
<div class="mb-6">
  <a href="{{ route('superadmin.projects.index') }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
    </svg>
    Quay lại Dự án
  </a>
</div>

<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h3 class="text-xl font-bold">{{ $project->name }}</h3>
      <p class="text-gray-600">{{ $project->code }}</p>
    </div>
    <span class="px-3 py-1 text-sm font-semibold rounded-full 
      {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
      {{ ucfirst($project->status) }}
    </span>
  </div>
  
  @if($remoteStats)
  <div class="border-t pt-4 mt-4">
    <h4 class="font-semibold text-gray-700 mb-3">Thống kê Remote Server</h4>
    <div class="grid grid-cols-4 gap-3">
      <div class="bg-blue-50 p-3 rounded-lg">
        <p class="text-sm text-gray-600">Users</p>
        <p class="text-2xl font-bold text-blue-600">{{ $remoteStats['users'] ?? 0 }}</p>
      </div>
      <div class="bg-green-50 p-3 rounded-lg">
        <p class="text-sm text-gray-600">Products</p>
        <p class="text-2xl font-bold text-green-600">{{ $remoteStats['products'] ?? 0 }}</p>
      </div>
      <div class="bg-blue-50 p-3 rounded-lg">
        <p class="text-sm text-gray-600">Orders</p>
        <p class="text-2xl font-bold text-blue-600">{{ $remoteStats['orders'] ?? 0 }}</p>
      </div>
      <div class="bg-orange-50 p-3 rounded-lg">
        <p class="text-sm text-gray-600">Posts</p>
        <p class="text-2xl font-bold text-orange-600">{{ $remoteStats['posts'] ?? 0 }}</p>
      </div>
    </div>
  </div>
  @endif
  
  <div class="border-t pt-4 mt-4">
    <h4 class="font-semibold text-gray-700 mb-3">Thông tin Truy cập</h4>
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
      <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="flex-1">
          <p class="text-sm text-blue-800 font-medium mb-2">Hướng dẫn đăng nhập:</p>
          <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside">
            <li>Truy cập Login URL bên dưới</li>
            <li>Đăng nhập với Username và Mật khẩu ở phần "Thông tin tài khoản"</li>
            <li>Sau khi đăng nhập thành công sẽ vào Admin Panel</li>
          </ol>
        </div>
      </div>
    </div>
    <div class="grid grid-cols-1 gap-3">
      @if($project->api_token)
      <div class="border rounded-lg p-3 bg-yellow-50">
        <label class="text-sm font-medium text-gray-700">API Token (Remote Control):</label>
        <div class="flex items-center gap-2 mt-1">
          <code class="flex-1 text-xs bg-gray-900 text-green-400 p-2 rounded font-mono break-all">{{ $project->api_token }}</code>
          <button onclick="copyToClipboard('{{ $project->api_token }}')" 
              class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs">
            Copy
          </button>
        </div>
        <p class="text-xs text-gray-600 mt-2">Sử dụng token này để SuperAdmin control project từ xa</p>
      </div>
      @endif
      <div class="border rounded-lg p-3">
        <label class="text-sm font-medium text-gray-700">Login URL:</label>
        <div class="flex items-center gap-2 mt-1">
          <a href="{{ route('project.login', $project->code) }}" target="_blank" 
            class="flex-1 text-blue-600 hover:text-blue-700 font-mono text-sm break-all">
            {{ route('project.login', $project->code) }}
          </a>
          <button onclick="copyToClipboard('{{ route('project.login', $project->code) }}')" 
              class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs">
            Copy
          </button>
        </div>
      </div>
      <div class="border rounded-lg p-3 bg-gray-50">
        <label class="text-sm font-medium text-gray-700">Admin Panel (sau khi đăng nhập):</label>
        <p class="text-gray-600 font-mono text-sm mt-1 break-all">
          {{ route('project.admin.dashboard', $project->code) }}
        </p>
      </div>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <!-- Cột trái: Thông tin tài khoản -->
  <div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-bold">Thông tin tài khoản</h3>
      <button type="button" onclick="document.getElementById('resetAccountModal').classList.remove('hidden')" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tạo / Đổi mật khẩu
      </button>
    </div>
    
    @if($users->isNotEmpty())
    <div class="space-y-3">
      @foreach($users as $user)
      <div class="border rounded-lg p-4 hover:border-blue-200 transition-colors">
        <div class="flex items-start justify-between mb-2">
          <div class="flex-1">
            <h5 class="font-semibold text-gray-900">{{ $user->name }}</h5>
            <p class="text-sm text-gray-600">{{ $user->email }}</p>
          </div>
          <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
            {{ ucfirst($user->role ?? 'user') }}
          </span>
        </div>
        <div class="grid grid-cols-2 gap-2 text-sm">
          <div>
            <span class="text-gray-500">Username:</span>
            <p class="font-mono text-gray-900">{{ $user->username }}</p>
          </div>
          <div>
            <span class="text-gray-500">Mật khẩu:</span>
            @php $plainPwd = $project->getDecryptedPassword(); @endphp
            @if($user->username == $project->project_admin_username && $plainPwd)
              <p class="font-mono text-blue-600 font-semibold">{{ $plainPwd }}</p>
            @else
              <p class="text-gray-400">***</p>
            @endif
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @else
    <p class="text-gray-500 text-center py-8">Chưa có tài khoản nào</p>
    @endif
  </div>

  <!-- Cột phải: Tabs -->
  <div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ route('superadmin.projects.config', $project) }}">
      @csrf
      
      <!-- Tab Navigation -->
      <div class="flex border-b mb-4">
        <button type="button" id="tab-btn-config" class="tab-button active px-4 py-2 border-b-2 border-blue-600 text-blue-600 font-semibold flex items-center gap-2" onclick="showTab('config', this)">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          Cấu hình CMS
        </button>
        <button type="button" id="tab-btn-features" class="tab-button px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center gap-2" onclick="showTab('features', this)">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
          </svg>
          Feature Packs
          @php $activeFeatureCount = count(is_array($project->cms_features) ? $project->cms_features : json_decode($project->cms_features ?? '[]', true) ?? []); @endphp
          @if($activeFeatureCount > 0)
            <span class="bg-blue-600 text-white text-xs font-bold rounded-full px-1.5 py-0.5 min-w-[20px] text-center">{{ $activeFeatureCount }}</span>
          @endif
        </button>
        <button type="button" id="tab-btn-history" class="tab-button px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center gap-2" onclick="showTab('history', this)">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          Lịch sử
        </button>
      </div>
      
      <!-- Config Tab -->
      <div id="config-tab" class="tab-content">
        <div class="space-y-6 max-h-[600px] overflow-y-auto pr-2">
          
          <!-- Feature Packs are now in features-tab -->
          
          <div>
            <h4 class="font-bold text-gray-800 mb-3 flex items-center">
              <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              </svg>
              Core Modules
            </h4>
            <div class="space-y-3">
          @foreach($systemModules as $module)
          <div class="border rounded-lg p-4 hover:border-blue-300 transition-colors">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <h5 class="font-semibold text-gray-800 mb-1">{{ $module['title'] }}</h5>
                <p class="text-sm text-gray-500">{{ $module['description'] }}</p>
              </div>
              <label class="toggle-switch ml-4">
                <input type="checkbox" name="settings[{{ $module['key'] }}]" value="1" 
                  {{ isset($settings[$module['key']]) && $settings[$module['key']] == '1' ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
              </label>
            </div>
          </div>
          @endforeach
            </div>
          </div>
        </div>
        
        <div class="border-t pt-4 mt-4">
          <label class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg cursor-pointer hover:bg-blue-100 transition-colors">
            <input type="checkbox" name="sync_data" value="1" class="w-5 h-5 text-blue-600 rounded">
            <div class="flex-1">
              <span class="font-semibold text-blue-900">Đồng bộ dữ liệu từ Main DB</span>
              <p class="text-sm text-blue-700 mt-1">Copy settings, menus, widgets, posts, categories, brands từ database chính sang project database</p>
            </div>
          </label>
        </div>
        
        <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
          <a href="{{ route('superadmin.projects.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</a>
          <button type="button" onclick="this.form.submit()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu cấu hình</button>
        </div>
      </div>
      
      <!-- Features Tab -->
      <div id="features-tab" class="tab-content hidden">
        @php
          $currentFeatures = is_array($project->cms_features) ? $project->cms_features : json_decode($project->cms_features ?? '[]', true) ?? [];
          $currentFeatures = old('cms_features', $currentFeatures);
          $groupConfigs = config('feature_packs.groups', []);
          $groupedPacks = $featurePacks->groupBy('group_name');
          
          // Map group label -> config key for icon/color lookup
          $groupMeta = [];
          foreach ($groupConfigs as $key => $cfg) {
            $groupMeta[$cfg['label']] = [
              'icon' => $cfg['icon'] ?? '',
              'color' => $cfg['color'] ?? 'gray',
              'description' => $cfg['description'] ?? '',
            ];
          }
          $colorMap = [
            'red'  => ['bg' => 'bg-red-50',  'border' => 'border-red-200',  'title' => 'text-red-700',  'badge' => 'bg-red-100 text-red-700',  'check' => 'text-red-600'],
            'blue'  => ['bg' => 'bg-blue-50',  'border' => 'border-blue-200',  'title' => 'text-blue-700',  'badge' => 'bg-blue-100 text-blue-700',  'check' => 'text-blue-600'],
            'yellow' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'title' => 'text-yellow-700', 'badge' => 'bg-yellow-100 text-yellow-700', 'check' => 'text-yellow-600'],
            'green' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'title' => 'text-green-700', 'badge' => 'bg-green-100 text-green-700', 'check' => 'text-green-600'],
            'purple' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'title' => 'text-purple-700', 'badge' => 'bg-purple-100 text-purple-700', 'check' => 'text-purple-600'],
            'gray'  => ['bg' => 'bg-gray-50',  'border' => 'border-gray-200',  'title' => 'text-gray-700',  'badge' => 'bg-gray-100 text-gray-700',  'check' => 'text-gray-600'],
          ];
        @endphp

        @if($featurePacks->isEmpty())
          <div class="text-center py-12">
            <div class="text-5xl mb-3"></div>
            <p class="text-gray-500 font-medium">Chưa có Feature Pack nào.</p>
            <p class="text-gray-400 text-sm mt-1">Vui lòng chạy <code class="bg-gray-100 px-1 rounded">php artisan db:seed --class=FeaturePackSeeder</code></p>
          </div>
        @else
          {{-- Summary bar --}}
          <div class="flex items-center justify-between mb-4 px-1">
            <div class="flex items-center gap-2">
              <span class="text-sm font-semibold text-gray-700">Tính năng đã kích hoạt:</span>
              <span id="feature-count-badge" class="px-2.5 py-0.5 rounded-full text-sm font-bold bg-blue-600 text-white">{{ count($currentFeatures) }}</span>
              <span class="text-sm text-gray-500">/ {{ $featurePacks->count() }}</span>
            </div>
            <button type="button" onclick="toggleAllFeatures()" class="text-xs text-blue-600 hover:text-blue-800 underline">Bỏ chọn tất cả</button>
          </div>

          <div class="space-y-4 max-h-[520px] overflow-y-auto pr-1 pb-1">
          @foreach($groupedPacks as $groupName => $packs)
            @php
              $meta = $groupMeta[$groupName] ?? ['icon' => '', 'color' => 'gray', 'description' => ''];
              $colors = $colorMap[$meta['color']] ?? $colorMap['gray'];
              $activeCount = $packs->filter(fn($p) => in_array($p->code, $currentFeatures))->count();
            @endphp
            <div class="rounded-xl border-2 {{ $colors['border'] }} {{ $colors['bg'] }} overflow-hidden">
              {{-- Group Header --}}
              <div class="flex items-center justify-between px-4 py-3 border-b {{ $colors['border'] }} bg-white/60">
                <div class="flex items-center gap-2">
                  <span class="text-xl">{{ $meta['icon'] }}</span>
                  <div>
                    <h4 class="font-bold text-sm {{ $colors['title'] }}">{{ $groupName }}</h4>
                    @if($meta['description'])
                      <p class="text-xs text-gray-500">{{ $meta['description'] }}</p>
                    @endif
                  </div>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $colors['badge'] }}">
                  {{ $activeCount }}/{{ $packs->count() }} tính năng
                </span>
              </div>

              {{-- Feature Cards --}}
              <div class="p-3 grid grid-cols-1 gap-2">
                @foreach($packs as $pack)
                  @php $isChecked = in_array($pack->code, $currentFeatures); @endphp
                  <label class="feature-card flex items-start gap-3 p-3 rounded-lg cursor-pointer border-2 transition-all duration-200
                    {{ $isChecked 
                      ? 'bg-white border-' . $meta['color'] . '-400 shadow-sm' 
                      : 'bg-white/50 border-transparent hover:border-gray-300 hover:bg-white' }}"
                    id="label-{{ $pack->code }}">
                    <input type="checkbox"
                      name="cms_features[]"
                      value="{{ $pack->code }}"
                      class="feature-checkbox mt-0.5 w-4 h-4 rounded border-gray-300 {{ $colors['check'] }} focus:ring-2"
                      {{ $isChecked ? 'checked' : '' }}
                      onchange="onFeatureChange(this, '{{ $meta['color'] }}')">
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-semibold text-gray-800 leading-tight">{{ $pack->name }}</p>
                      @if($pack->description)
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $pack->description }}</p>
                      @endif
                    </div>
                    @if($isChecked)
                      <svg class="feature-check-icon w-4 h-4 {{ $colors['check'] }} shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      </svg>
                    @else
                      <svg class="feature-check-icon w-4 h-4 text-transparent shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      </svg>
                    @endif
                  </label>
                @endforeach
              </div>
            </div>
          @endforeach
          </div>
        @endif

        <div class="flex justify-end gap-3 mt-4 pt-4 border-t">
          <a href="{{ route('superadmin.projects.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Hủy</a>
          <button type="button" onclick="this.form.submit()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"> Lưu Feature Packs</button>
        </div>
      </div>
    </form>
    
    <!-- History Tab -->
    <div id="history-tab" class="tab-content hidden">
      <div class="mb-4 flex flex-wrap gap-2 items-center justify-between">
        <div class="flex gap-2">
          <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2" onclick="refreshHistory()">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Refresh
        </button>
        </div>
        <div class="flex items-center gap-3 bg-gray-50 px-3 py-2 rounded-lg border">
          <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Khoảng thời gian:</span>
          <input type="date" id="history-start-date" class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-blue-500 focus:border-blue-500" title="Từ ngày">
          <span class="text-gray-400">-</span>
          <input type="date" id="history-end-date" class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-blue-500 focus:border-blue-500" title="Đến ngày">
          <button type="button" onclick="loadHistory()" class="px-3 py-1 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700 transition-colors">Lọc</button>
          <button type="button" onclick="document.getElementById('history-start-date').value=''; document.getElementById('history-end-date').value=''; loadHistory();" class="text-xs text-gray-500 hover:text-red-500">Xóa lọc</button>
        </div>
      </div>
      
      <div id="history-content">
        <div class="text-center py-8">
          <div class="spinner-border" role="status"></div>
          <p class="text-gray-500 mt-2">Đang tải lịch sử...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Reset Account -->
<div id="resetAccountModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
    <div class="flex justify-between items-center mb-4">
      <h4 class="text-lg font-bold">Tạo / Đổi mật khẩu</h4>
      <button onclick="document.getElementById('resetAccountModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
      </button>
    </div>
    <form method="POST" action="{{ route('superadmin.projects.reset-admin', $project) }}">
      @csrf
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
          <input type="text" name="username" value="{{ $project->code }}" required class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" name="email" value="{{ 'admin@' . $project->code . '.com' }}" required class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
          <input type="password" name="password" required minlength="6" class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="pt-4 flex justify-end gap-2">
          <button type="button" onclick="document.getElementById('resetAccountModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-50">Hủy</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu thay đổi</button>
        </div>
      </div>
    </form>
  </div>
</div>

<style>
.toggle-switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 24px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #cbd5e1;
  transition: .3s;
  border-radius: 24px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .3s;
  border-radius: 50%;
}

input:checked + .toggle-slider {
  background-color: #7c3aed;
}

input:checked + .toggle-slider:before {
  transform: translateX(24px);
}

.toggle-slider:hover {
  background-color: #94a3b8;
}

input:checked + .toggle-slider:hover {
  background-color: #6d28d9;
}
</style>

<script>
// Utility functions
function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    alert('Đã copy link!');
  });
}

function showNotification(message, type = 'info') {
  const existing = document.querySelectorAll('.notification-toast');
  existing.forEach(n => n.remove());
  
  const colors = {
    success: 'bg-green-100 border-green-200 text-green-800',
    error: 'bg-red-100 border-red-200 text-red-800',
    warning: 'bg-yellow-100 border-yellow-200 text-yellow-800',
    info: 'bg-blue-100 border-blue-200 text-blue-800'
  };
  
  const notification = document.createElement('div');
  notification.className = `notification-toast fixed top-4 right-4 ${colors[type]} border rounded-lg p-4 shadow-lg z-50 max-w-sm`;
  notification.innerHTML = `
    <div class="flex items-start">
      <div class="flex-1 text-sm font-medium">${message}</div>
      <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-gray-400 hover:text-gray-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
  `;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    if (notification.parentNode) {
      notification.remove();
    }
  }, 5000);
}

function showProcessingStatus(step, message, progress = null) {
  const statusHtml = `
    <div class="text-center py-8">
      <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-100 to-blue-100 text-blue-800 rounded-lg mb-4 shadow-sm">
        <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <div class="text-left">
          <div class="font-semibold">Bước ${step}: ${message}</div>
          ${progress ? `<div class="text-xs mt-1 opacity-75">${progress}</div>` : ''}
        </div>
      </div>
      <div class="space-y-2 text-sm text-gray-600">
        <div class="flex items-center justify-center space-x-4">
          <div class="flex items-center ${step >= 1 ? 'text-green-600' : 'text-gray-400'}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Kết nối API
          </div>
          <div class="flex items-center ${step >= 2 ? 'text-green-600' : 'text-gray-400'}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Nhận dữ liệu
          </div>
          <div class="flex items-center ${step >= 3 ? 'text-green-600' : 'text-gray-400'}">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Xử lý & hiển thị
          </div>
        </div>
      </div>
    </div>
  `;
  
  document.getElementById('history-content').innerHTML = statusHtml;
}

function getMethodColor(method) {
  const colors = {
    'GET': 'bg-blue-100 text-blue-800',
    'POST': 'bg-green-100 text-green-800',
    'PUT': 'bg-yellow-100 text-yellow-800',
    'PATCH': 'bg-orange-100 text-orange-800',
    'DELETE': 'bg-red-100 text-red-800'
  };
  return colors[method] || 'bg-gray-100 text-gray-800';
}

function getTimeAgo(date) {
  const now = new Date();
  const diffInSeconds = Math.floor((now - date) / 1000);
  
  if (diffInSeconds < 60) return 'Vừa xong';
  if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' phút trước';
  if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' giờ trước';
  if (diffInSeconds < 2592000) return Math.floor(diffInSeconds / 86400) + ' ngày trước';
  
  return date.toLocaleDateString('vi-VN');
}

// Tab management
function showTab(tabName, btnEl) {
  document.querySelectorAll('.tab-content').forEach(tab => {
    tab.classList.add('hidden');
  });
  
  document.querySelectorAll('.tab-button').forEach(btn => {
    btn.classList.remove('active', 'border-blue-600', 'text-blue-600', 'font-semibold');
    btn.classList.add('border-transparent', 'text-gray-500');
  });
  
  document.getElementById(tabName + '-tab').classList.remove('hidden');
  
  // Use the passed button element (fixes SVG child click issue)
  var activeBtn = btnEl || document.getElementById('tab-btn-' + tabName);
  if (activeBtn) {
    activeBtn.classList.add('active', 'border-blue-600', 'text-blue-600', 'font-semibold');
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
  }
  
  if (tabName === 'history') {
    loadHistory();
  }
}

// Feature Pack toggle
function onFeatureChange(checkbox, colorKey) {
  var label = checkbox.closest('label');
  var icon = label.querySelector('.feature-check-icon');
  var borderColorMap = {
    'red': 'border-red-400', 'blue': 'border-blue-400',
    'yellow': 'border-yellow-400', 'green': 'border-green-400',
    'purple': 'border-purple-400', 'gray': 'border-gray-400'
  };
  var iconColorMap = {
    'red': 'text-red-600', 'blue': 'text-blue-600',
    'yellow': 'text-yellow-600', 'green': 'text-green-600',
    'purple': 'text-purple-600', 'gray': 'text-gray-600'
  };
  
  if (checkbox.checked) {
    label.classList.add('bg-white', 'shadow-sm', borderColorMap[colorKey] || 'border-blue-400');
    label.classList.remove('bg-white/50', 'border-transparent');
    if (icon) {
      icon.classList.remove('text-transparent');
      icon.classList.add(iconColorMap[colorKey] || 'text-blue-600');
    }
  } else {
    label.classList.remove('bg-white', 'shadow-sm', borderColorMap[colorKey] || 'border-blue-400');
    label.classList.add('bg-white/50', 'border-transparent');
    if (icon) {
      icon.classList.add('text-transparent');
      icon.classList.remove(iconColorMap[colorKey] || 'text-blue-600');
    }
  }
  
  // Update count badge
  var total = document.querySelectorAll('.feature-checkbox:checked').length;
  var badge = document.getElementById('feature-count-badge');
  if (badge) badge.textContent = total;
}

// Toggle all feature checkboxes
var allFeaturesSelected = false;
function toggleAllFeatures() {
  allFeaturesSelected = !allFeaturesSelected;
  document.querySelectorAll('.feature-checkbox').forEach(cb => {
    if (cb.checked !== allFeaturesSelected) {
      cb.checked = allFeaturesSelected;
      onFeatureChange(cb, cb.closest('[data-color]')?.dataset.color || 'blue');
    }
  });
  var btn = event.target;
  btn.textContent = allFeaturesSelected ? 'Bỏ chọn tất cả' : 'Chọn tất cả';
}

// History management
function loadHistory() {
  console.log('Loading history for project: {{ $project->code }}');
  
  let url = '/superadmin/file-monitor?project={{ $project->code }}';
  const startDate = document.getElementById('history-start-date');
  const endDate = document.getElementById('history-end-date');
  
  if (startDate && startDate.value && endDate && endDate.value) {
    if (new Date(startDate.value) > new Date(endDate.value)) {
      alert('Lỗi: Ngày bắt đầu không được lớn hơn ngày kết thúc!');
      return;
    }
  }

  if (startDate && startDate.value) {
    url += '&start_date=' + startDate.value;
  }
  if (endDate && endDate.value) {
    url += '&end_date=' + endDate.value;
  }
  
  showProcessingStatus(1, 'Khởi tạo kết nối', 'Đang quét file log: storage/logs/file-changes-{{ $project->code }}.log');
  
  fetch(url, {
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => {
    console.log('Response status:', response.status);
    showProcessingStatus(2, 'Nhận dữ liệu thành công', `API Status: ${response.status} - Đang parse JSON response...`);
    return response.json();
  })
  .then(data => {
    console.log('History data:', data);
    showProcessingStatus(3, 'Xử lý dữ liệu', `Tổng số logs: ${data.total || (data.logs ? data.logs.length : 0)} - Đang format hiển thị...`);
    
    const logs = data.logs || data || [];
    console.log('Processed logs:', logs);
    
    setTimeout(() => {
      if (logs && logs.length > 0) {
        displayLogs(logs);
      } else {
        showEmptyState();
      }
    }, 800);
  })
  .catch(error => {
    console.error('Error loading history:', error);
    showErrorState(error);
  });
}

function displayLogs(logs) {
  let historyHtml = '<div class="space-y-3 max-h-[500px] overflow-y-auto">';
  
  logs.forEach(log => {
    const date = new Date(log.timestamp);
    const timeAgo = getTimeAgo(date);
    
    historyHtml += `
      <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
        <div class="flex items-start justify-between mb-2">
          <div class="flex-1">
            <h5 class="font-semibold text-gray-900">${log.action || 'Thay đổi'}</h5>
            <p class="text-sm text-gray-600">${log.route || log.url}</p>
          </div>
          <span class="px-2 py-1 text-xs font-semibold rounded-full ${getMethodColor(log.method)}">
            ${log.method}
          </span>
        </div>
        <div class="flex items-center justify-between text-sm text-gray-500">
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            ${log.user_name || 'Khách'} ${log.user_email ? `(${log.user_email})` : ''}
          </span>
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            ${date.toLocaleString('vi-VN')} <span class="text-xs text-gray-400">(${timeAgo})</span>
          </span>
        </div>
        ${log.data_summary && Object.keys(log.data_summary).length > 0 ? `
          <div class="mt-3 p-2 bg-gray-100 rounded text-xs overflow-x-auto whitespace-pre-wrap max-h-32">
            <strong>Dữ liệu:</strong> ${
              Object.entries(log.data_summary).map(([key, value]) => `<br/>- <b>${key}:</b> ${value}`).join('')
            }
          </div>
        ` : ''}
      </div>
    `;
  });
  
  historyHtml += '</div>';
  
  const summaryHtml = `
    <div class="mb-6 grid grid-cols-3 gap-4">
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
          <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <div>
            <p class="text-sm font-medium text-blue-900">Tổng số logs</p>
            <p class="text-2xl font-bold text-blue-600">${logs.length}</p>
          </div>
        </div>
      </div>
      <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
          <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div>
            <p class="text-sm font-medium text-green-900">Log mới nhất</p>
            <p class="text-sm font-bold text-green-600">${getTimeAgo(new Date(logs[0].timestamp))}</p>
          </div>
        </div>
      </div>
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
          <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          <div>
            <p class="text-sm font-medium text-blue-900">Project</p>
            <p class="text-sm font-bold text-blue-600">{{ $project->code }}</p>
          </div>
        </div>
      </div>
    </div>
  `;
  
  document.getElementById('history-content').innerHTML = summaryHtml + historyHtml;
  showNotification(' Đã tải thành công ' + logs.length + ' log entries', 'success');
}

function showEmptyState() {
  document.getElementById('history-content').innerHTML = `
    <div class="text-center py-12">
      <div class="mb-4">
        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có lịch sử chỉnh sửa</h3>
      <p class="text-sm text-gray-500 mb-4">Các thay đổi sẽ được ghi lại tự động khi bạn thực hiện các hành động.</p>
      
      <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 max-w-md mx-auto">
        <div class="flex items-start">
          <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          <div class="text-left">
            <h4 class="text-sm font-medium text-yellow-800">Để tạo log mẫu:</h4>
            <ul class="mt-2 text-xs text-yellow-700 space-y-1">
              <li>• Thực hiện thay đổi cấu hình</li>
              <li>• Tạo/sửa sản phẩm, bài viết</li>
              <li>• Hoặc <a href="/superadmin/test-logging" target="_blank" class="underline">test logging</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  `;
  
  showNotification('ℹ️ Không tìm thấy log nào cho project này', 'info');
}

function showErrorState(error) {
  document.getElementById('history-content').innerHTML = `
    <div class="text-center py-8">
      <div class="bg-red-50 border border-red-200 rounded-lg p-6 max-w-md mx-auto">
        <div class="text-red-600 mb-4">
          <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-red-900 mb-2">Lỗi tải lịch sử</h3>
        <p class="text-red-700 mb-4">${error.message}</p>
        
        <div class="flex gap-2">
          <button onclick="loadHistory()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
             Thử lại
          </button>
          <a href="/superadmin/debug-history" target="_blank" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm">
             Debug
          </a>
        </div>
      </div>
    </div>
  `;
  
  showNotification(' Lỗi tải lịch sử: ' + error.message, 'error');
}

function refreshHistory() {
  showNotification(' Đang refresh lịch sử...', 'info');
  loadHistory();
}

// Export menu management
function toggleExportMenu() {
  const menu = document.getElementById('export-menu');
  menu.classList.toggle('hidden');
}

// Close export menu when clicking outside
document.addEventListener('click', function(event) {
  const menu = document.getElementById('export-menu');
  const button = event.target.closest('button');
  
  if (!button || !button.onclick || button.onclick.toString().indexOf('toggleExportMenu') === -1) {
    menu.classList.add('hidden');
  }
});
</script>
@endsection
