<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin')</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
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

        // Xử lý resize sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar-resizable');
            if(sidebar) {
                new ResizeObserver(entries => {
                    for (let entry of entries) {
                        document.documentElement.style.setProperty('--sidebar-width', entry.contentRect.width + 'px');
                    }
                }).observe(sidebar);
            }
        });
    </script>
    <style>
        :root {
            --sidebar-width: 18rem; /* 72 * 0.25rem = 18rem */
        }
        .sidebar-resizable {
            width: var(--sidebar-width);
            min-width: 15rem;
            max-width: 30rem;
            resize: horizontal;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .content-resizable {
            margin-left: var(--sidebar-width);
        }
        /* Style cho thanh kéo resize */
        .sidebar-resizable::-webkit-resizer {
            background-color: #002D80;
            border-left: 1px solid #ffffff33;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-gray-800">
    <div class="min-h-screen flex w-full">
        <div class="sidebar-resizable bg-[#001B4E] shadow-2xl fixed h-screen">
            <div class="p-6 border-b border-[#002D80]">
                <div class="flex items-center justify-center py-6 px-4">
                    <img src="{{ asset('Logo.png') }}" alt="AIM AGENCY" class="h-20 w-full object-contain">
                </div>
            </div>

            <nav class="mt-6 px-3">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-4 py-3 mb-2 text-gray-300 hover:bg-[#002D80] rounded-lg {{ request()->routeIs('superadmin.dashboard') ? 'bg-[#002D80]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="ml-3 font-medium">Dashboard</span>
                </a>

                @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('superadmin.multi-tenancy') }}" class="flex items-center px-4 py-3 mb-2 text-gray-300 hover:bg-[#002D80] rounded-lg {{ request()->routeIs('superadmin.multi-tenancy') ? 'bg-[#002D80]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"></path>
                    </svg>
                    <span class="ml-3 font-medium">Multi-Tenancy</span>
                </a>
                @endif

                @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('superadmin.users.index') }}" class="flex items-center px-4 py-3 mb-2 text-gray-300 hover:bg-[#002D80] rounded-lg {{ request()->routeIs('superadmin.users.*') ? 'bg-[#002D80]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span class="ml-3 font-medium">Quản lý Nhân sự</span>
                </a>

                <a href="{{ route('superadmin.roles.index') }}" class="flex items-center px-4 py-3 mb-2 text-gray-300 hover:bg-[#002D80] rounded-lg {{ request()->routeIs('superadmin.roles.*') ? 'bg-[#002D80]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span class="ml-3 font-medium">Vai trò (Roles)</span>
                </a>

                <a href="{{ route('superadmin.permissions.index') }}" class="flex items-center px-4 py-3 mb-2 text-gray-300 hover:bg-[#002D80] rounded-lg {{ request()->routeIs('superadmin.permissions.*') ? 'bg-[#002D80]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                    </svg>
                    <span class="ml-3 font-medium">Quyền hạn (Permissions)</span>
                </a>
                @endif

                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('manage-contracts'))
                <a href="{{ route('superadmin.contracts.index') }}" class="flex items-center px-4 py-3 mb-2 text-gray-300 hover:bg-[#002D80] rounded-lg {{ request()->routeIs('superadmin.contracts.*') ? 'bg-[#002D80]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="ml-3 font-medium">Quản lý Hợp đồng</span>
                </a>
                @endif

                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('manage-briefs'))
                <a href="{{ route('superadmin.briefs.index') }}" class="flex items-center px-4 py-3 mb-2 text-gray-300 hover:bg-[#002D80] rounded-lg {{ request()->routeIs('superadmin.briefs.*') ? 'bg-[#002D80]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="ml-3 font-medium">Quản lý Brief</span>
                </a>
                @endif

                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('manage-projects') || auth()->user()->role === 'dev' || auth()->user()->hasRole('dev'))
                <a href="{{ route('superadmin.projects.index') }}" class="flex items-center px-4 py-3 mb-2 text-gray-300 hover:bg-[#002D80] rounded-lg {{ request()->routeIs('superadmin.projects.*') ? 'bg-[#002D80]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span class="ml-3 font-medium">Quản lý Dự án</span>
                </a>
                
                @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('superadmin.feature-packs.index') }}" class="flex items-center px-4 py-3 mb-2 text-gray-300 hover:bg-[#002D80] rounded-lg {{ request()->routeIs('superadmin.feature-packs.*') ? 'bg-[#002D80]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span class="ml-3 font-medium">Gói Tính Năng (Feature Packs)</span>
                </a>
                @endif
                @endif

                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('manage-tasks') || auth()->user()->hasPermission('update-tasks-progress') || auth()->user()->hasPermission('review-tasks') || auth()->user()->role === 'dev' || auth()->user()->hasRole('dev'))
                <a href="{{ route('superadmin.tickets.index') }}" class="flex items-center px-4 py-3 mb-2 text-gray-300 hover:bg-[#002D80] rounded-lg {{ request()->routeIs('superadmin.tickets.*') ? 'bg-[#002D80]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span class="ml-3 font-medium">Hỗ trợ / Tickets</span>
                </a>
                @endif

                @if(auth()->user()->isSuperAdmin() || auth()->user()->role === 'dev' || auth()->user()->hasRole('dev'))
                <a href="{{ route('superadmin.multi-tenancy') }}" class="flex items-center px-4 py-3 mb-2 text-gray-300 hover:bg-[#002D80] rounded-lg {{ request()->routeIs('superadmin.multi-tenancy*') ? 'bg-[#002D80]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="ml-3 font-medium">Multi Dự án</span>
                </a>
                @endif


            </nav>

            <div class="mt-auto p-4 border-t border-[#002D80] absolute bottom-0 w-full">
                <div class="text-gray-400 text-xs text-center space-y-1">
                    <p class="font-semibold">Super Admin Panel</p>
                    <p>© 2025 AIM AGENCY</p>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col content-resizable">
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
                    {!! nl2br(e(session('alert.message'))) !!}
                </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
