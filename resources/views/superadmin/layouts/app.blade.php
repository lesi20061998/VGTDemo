<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Bỏ qua lỗi từ browser extensions
        window.addEventListener('error', function(e) {
            if (e.message && e.message.includes('message channel closed')) {
                e.preventDefault();
                return false;
            }
        });
        
        window.addEventListener('unhandledrejection', function(e) {
            if (e.reason && e.reason.message && e.reason.message.includes('message channel closed')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex w-full">
        <div class="w-72 bg-gradient-to-b from-purple-900 to-purple-700 shadow-2xl fixed h-screen overflow-y-auto">
            <div class="p-6 border-b border-purple-600">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-yellow-400 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h2 class="text-white font-bold">SUPER ADMIN</h2>
                        <p class="text-purple-200 text-xs">Quản trị hệ thống</p>
                    </div>
                </div>
            </div>

            <nav class="mt-6 px-3">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-4 py-3 mb-2 text-purple-100 hover:bg-purple-600 rounded-lg {{ request()->routeIs('superadmin.dashboard') ? 'bg-purple-600' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="ml-3 font-medium">Dashboard</span>
                </a>

                <a href="{{ route('superadmin.multi-tenancy') }}" class="flex items-center px-4 py-3 mb-2 text-purple-100 hover:bg-purple-600 rounded-lg {{ request()->routeIs('superadmin.multi-tenancy') ? 'bg-purple-600' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"></path>
                    </svg>
                    <span class="ml-3 font-medium">Multi-Tenancy</span>
                </a>

                <a href="{{ route('superadmin.employees.index') }}" class="flex items-center px-4 py-3 mb-2 text-purple-100 hover:bg-purple-600 rounded-lg {{ request()->routeIs('superadmin.employees.*') ? 'bg-purple-600' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span class="ml-3 font-medium">Quản lý Nhân sự</span>
                </a>

                <a href="{{ route('superadmin.contracts.index') }}" class="flex items-center px-4 py-3 mb-2 text-purple-100 hover:bg-purple-600 rounded-lg {{ request()->routeIs('superadmin.contracts.*') ? 'bg-purple-600' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="ml-3 font-medium">Quản lý Hợp đồng</span>
                </a>

                <a href="{{ route('superadmin.projects.index') }}" class="flex items-center px-4 py-3 mb-2 text-purple-100 hover:bg-purple-600 rounded-lg {{ request()->routeIs('superadmin.projects.*') ? 'bg-purple-600' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span class="ml-3 font-medium">Quản lý Dự án</span>
                </a>

                <a href="{{ route('superadmin.tasks.index') }}" class="flex items-center px-4 py-3 mb-2 text-purple-100 hover:bg-purple-600 rounded-lg {{ request()->routeIs('superadmin.tasks.*') ? 'bg-purple-600' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span class="ml-3 font-medium">Quản lý Task</span>
                </a>

                <a href="{{ route('superadmin.tickets.index') }}" class="flex items-center px-4 py-3 mb-2 text-purple-100 hover:bg-purple-600 rounded-lg {{ request()->routeIs('superadmin.tickets.*') ? 'bg-purple-600' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    <span class="ml-3 font-medium">Ticket & Feedback</span>
                </a>
            </nav>

            <div class="mt-auto p-4 border-t border-purple-600 absolute bottom-0 w-full">
                <div class="text-purple-200 text-xs text-center space-y-1">
                    <p class="font-semibold">Super Admin Panel</p>
                    <p>© 2025 VN Global Tech</p>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col ml-72">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Super Admin')</h1>
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-purple-600">Super Administrator</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-red-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6 bg-gray-50">
                @if(session('alert'))
                <div class="mb-6 p-4 rounded-lg {{ session('alert.type') === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ session('alert.message') }}
                </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
