<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Satellite System') }} - @yield('title')</title>

    <!-- Vite & Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex h-screen overflow-hidden">

    <!-- Sidebar Modern -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col hidden md:flex transition-all duration-300">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800">
            <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center mr-3 shadow-lg">
                <i class="fas fa-satellite text-white text-sm"></i>
            </div>
            <span class="text-lg font-bold tracking-wide text-white">Orbit<span class="text-indigo-400">Sys</span></span>
        </div>
        
        <!-- Menu -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white rounded-xl shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all' }}">
                <i class="fas fa-border-all w-6 text-center"></i>
                <span class="font-medium ml-2">Dashboard</span>
            </a>
            
            <div class="pt-6 pb-2">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider px-4">Database</p>
            </div>
            
            <a href="{{ route('satellites.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('satellites.*') ? 'bg-indigo-600 text-white rounded-xl shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all' }}">
                <i class="fas fa-satellite w-6 text-center"></i>
                <span class="font-medium ml-2">Satellites</span>
            </a>
            
            <a href="{{ route('ground-stations.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('ground-stations.*') ? 'bg-indigo-600 text-white rounded-xl shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all' }}">
                <i class="fas fa-broadcast-tower w-6 text-center"></i>
                <span class="font-medium ml-2">Ground Stations</span>
            </a>

            <div class="pt-6 pb-2">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider px-4">Analytics</p>
            </div>

            <a href="{{ route('statistics') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('statistics') ? 'bg-indigo-600 text-white rounded-xl shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all' }}">
                <i class="fas fa-chart-pie w-6 text-center"></i>
                <span class="font-medium ml-2">Statistics</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-y-auto bg-slate-50">
        <!-- Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 z-10 sticky top-0">
            <div class="flex items-center">
                <h1 class="text-xl font-bold text-gray-800">@yield('page-title')</h1>
            </div>
            
            <div class="flex items-center space-x-4 relative">
                <!-- User Profile Button -->
                <button id="profileBtn" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->name ?? 'Administrator' }}</p>
                        <p class="text-xs text-gray-500">System Admin</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold">
                        {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-xs ml-1"></i>
                </button>

                <!-- Dropdown Menu -->
                <div id="profileDropdown" class="hidden absolute right-0 top-full mt-1 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50 transition-all">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-800">My Account</p>
                    </div>
                    
                    <a href="#" class="block px-4 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 transition-colors mt-1">
                        <i class="fas fa-user mr-2 w-4 text-center"></i> Profile
                    </a>
                    
                    <a href="{{ route('logout') }}" 
                       class="block px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 transition-colors"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt mr-2 w-4 text-center"></i> Logout
                    </a>
                    
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </header>

        <!-- Dynamic Content -->
        <main class="p-8">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center">
                    <i class="fas fa-check-circle mr-3 text-emerald-500"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-rose-500"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Script untuk Dropdown Profil -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileBtn = document.getElementById('profileBtn');
            const profileDropdown = document.getElementById('profileDropdown');

            if(profileBtn && profileDropdown) {
                // Toggle menu saat diklik
                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('hidden');
                });

                // Tutup menu saat klik di luar area dropdown
                document.addEventListener('click', function(e) {
                    if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>