<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false, showLogout: false }"
      :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Barangay Connect') }} &mdash; @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}"/>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100" x-cloak>

<div class="flex h-screen overflow-hidden">

    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen" @click="sidebarOpen=false"
         class="fixed inset-0 z-20 bg-black/50 lg:hidden" x-cloak></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-30 w-64 flex-shrink-0 flex flex-col bg-indigo-700 dark:bg-indigo-950
                  transform transition-transform duration-200 lg:static lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="flex items-center gap-3 px-5 py-4 border-b border-white/10">
            <img src="{{ asset('logo.png') }}" alt="BarangayConnect Logo"
                 class="w-10 h-10 rounded-lg object-cover flex-shrink-0 bg-white/10"/>
            <div>
                <p class="text-white font-bold text-sm leading-tight">Barangay Connect</p>
                <p class="text-indigo-200 text-xs capitalize">{{ auth()->user()->role }}</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-indigo-100">
            @include('layouts.sidebar-' . auth()->user()->role)
        </nav>

        {{-- Sidebar bottom: dark mode toggle --}}
        <div class="p-3 border-t border-white/10">
            <button @click="darkMode=!darkMode;localStorage.setItem('darkMode',darkMode)"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-indigo-200
                           hover:bg-white/10 hover:text-white transition-colors">
                <svg x-show="!darkMode" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg x-show="darkMode" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
            </button>
        </div>

    </aside>

    {{-- Main content area --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="relative z-40 flex items-center justify-between bg-white dark:bg-gray-800 border-b dark:border-gray-700 px-4 py-3 shadow-sm">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen=!sidebarOpen" class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-base font-semibold text-gray-800 dark:text-white">@yield('title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-2">

                {{-- Notification Bell Dropdown --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">

                    {{-- Bell button --}}
                    <button @click="open = !open"
                            class="relative p-2 rounded-full text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if(($unreadNotificationsCount ?? 0) > 0)
                            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white dark:border-gray-800 badge-pulse"></span>
                        @endif
                    </button>

                    {{-- Dropdown panel --}}
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-xl shadow-xl
                                border border-gray-200 dark:border-gray-700 z-50 overflow-hidden origin-top-right">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
                            @if(($unreadNotificationsCount ?? 0) > 0)
                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                @csrf
                                <button type="submit"
                                        class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                    Mark all as read
                                </button>
                            </form>
                            @endif
                        </div>

                        {{-- Notification items --}}
                        <div class="max-h-[420px] overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($navNotifications ?? [] as $notif)
                            <a href="{{ $notif->reference_type ? route('notifications.view', $notif) : route('notifications.index') }}"
                               class="flex items-start gap-3 px-4 py-3 transition-colors
                                      {{ !$notif->read
                                          ? 'bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30'
                                          : 'hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">

                                {{-- Unread dot --}}
                                <div class="mt-1.5 flex-shrink-0 w-2.5">
                                    @if(!$notif->read)
                                        <span class="block w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium leading-snug truncate
                                              {{ !$notif->read ? 'text-gray-900 dark:text-white' : 'text-gray-700 dark:text-gray-300' }}">
                                        {{ $notif->title }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2 leading-relaxed">
                                        {{ $notif->message }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        {{ $notif->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </a>
                            @empty
                            <div class="px-4 py-10 text-center">
                                <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <p class="text-sm text-gray-400 dark:text-gray-500">No notifications yet.</p>
                            </div>
                            @endforelse
                        </div>

                        {{-- Footer --}}
                        <div class="border-t border-gray-100 dark:border-gray-700 px-4 py-2.5 bg-gray-50 dark:bg-gray-800/80">
                            <a href="{{ route('notifications.index') }}"
                               class="block text-center text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                See all notifications
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Profile Avatar Dropdown --}}
                <div class="relative" x-data="{ openProfile: false }" @click.outside="openProfile = false">

                    <button @click="openProfile = !openProfile"
                            class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center
                                   bg-indigo-100 dark:bg-indigo-700 text-indigo-700 dark:text-white text-sm font-bold
                                   ring-2 ring-transparent hover:ring-indigo-400 transition">
                        @if(!empty($navAvatarUrl))
                            <img src="{{ $navAvatarUrl }}" alt="Avatar" class="w-full h-full object-cover"/>
                        @else
                            {{ auth()->user()->avatar_initials }}
                        @endif
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="openProfile" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl
                                border border-gray-200 dark:border-gray-700 z-50 overflow-hidden origin-top-right">

                        {{-- User info --}}
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                {{ auth()->user()->email }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" @click="openProfile = false"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300
                                      hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                My Profile
                            </a>

                            <div class="border-t border-gray-100 dark:border-gray-700 mt-1 pt-1">
                                <button type="button"
                                        @click="openProfile = false; showLogout = true"
                                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 dark:text-red-400
                                               hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Sign Out
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success') || session('error') || $errors->any())
        <div class="px-6 pt-4">
            @if(session('success'))
                <div x-data="{s:true}" x-show="s" x-init="setTimeout(()=>s=false,4000)"
                     class="flex items-center justify-between bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-2.5 rounded-lg mb-3 text-sm">
                    <span>{{ session('success') }}</span>
                    <button @click="s=false" class="ml-4 font-bold">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{s:true}" x-show="s"
                     class="flex items-center justify-between bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-2.5 rounded-lg mb-3 text-sm">
                    <span>{{ session('error') }}</span>
                    <button @click="s=false" class="ml-4 font-bold">&times;</button>
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-2.5 rounded-lg mb-3 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif
        </div>
        @endif

        <main class="flex-1 overflow-y-auto px-6 py-4 page-enter">
            @yield('content')
        </main>
    </div>
</div>

{{-- Sign Out Confirmation Modal --}}
<div x-show="showLogout" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="showLogout = false">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50" @click="showLogout = false"></div>

    {{-- Dialog --}}
    <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop>

        {{-- Icon + Title --}}
        <div class="px-6 pt-6 pb-4 text-center">
            <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                <svg class="w-7 h-7 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Sign out?</h3>
            <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                Are you sure you want to sign out of your account?
            </p>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 px-6 pb-6">
            <button type="button" @click="showLogout = false"
                    class="flex-1 px-4 py-2.5 text-sm font-medium rounded-xl border border-gray-300 dark:border-gray-600
                           text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancel
            </button>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full px-4 py-2.5 text-sm font-medium rounded-xl
                               bg-red-500 hover:bg-red-600 active:bg-red-700
                               text-white transition-colors">
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</div>

@stack('scripts')
<script>
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var main = document.querySelector('main');
        if (!main) return;

        /* Stagger direct children of each .grid inside main */
        main.querySelectorAll('.grid').forEach(function (grid) {
            Array.from(grid.children).forEach(function (el, i) {
                el.style.animation = 'fadeInUp 0.45s ease ' + (0.04 + i * 0.08) + 's both';
            });
        });

        /* Stagger table rows */
        main.querySelectorAll('tbody').forEach(function (tbody) {
            Array.from(tbody.children).forEach(function (row, i) {
                if (i < 25) {
                    row.style.animation = 'fadeInUp 0.3s ease ' + (i * 0.035) + 's both';
                }
            });
        });

        /* Stagger space-y list cards (notifications, complaints, etc.) */
        main.querySelectorAll('.space-y-4, .space-y-6').forEach(function (list) {
            /* Skip if it's nested inside another animated container */
            if (list.closest('.space-y-4 .space-y-4')) return;
            Array.from(list.children).forEach(function (el, i) {
                if (i < 15) {
                    el.style.animation = 'fadeInUp 0.4s ease ' + (0.05 + i * 0.06) + 's both';
                }
            });
        });
    });
})();
</script>
</body>
</html>
