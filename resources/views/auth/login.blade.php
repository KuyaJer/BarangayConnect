<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In &mdash; {{ config('app.name', 'Barangay Connect') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900" x-data="{ showPw: false }">

<div class="min-h-screen flex">

    {{-- Left Panel: Branding --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-2/5 flex-col justify-between
                bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-700 p-12 relative overflow-hidden">

        {{-- Background decoration --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white/5"></div>
            <div class="absolute -bottom-32 -right-32 w-[30rem] h-[30rem] rounded-full bg-white/5"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                        w-64 h-64 rounded-full bg-white/5"></div>
        </div>

        {{-- Logo & App Name --}}
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center
                            text-white font-bold text-xl shadow-lg">B</div>
                <div>
                    <p class="text-white font-bold text-lg leading-none">Barangay Connect</p>
                    <p class="text-indigo-200 text-xs mt-0.5">Community Management System</p>
                </div>
            </div>
        </div>

        {{-- Main Tagline --}}
        <div class="relative z-10 flex-1 flex flex-col justify-center">
            <h1 class="text-4xl font-bold text-white leading-tight mb-4">
                Connecting<br/>residents and<br/>local government.
            </h1>
            <p class="text-indigo-200 text-base leading-relaxed max-w-sm">
                Submit service requests, file complaints, report maintenance issues,
                and stay updated with the latest barangay announcements — all in one place.
            </p>

            <div class="mt-10 grid grid-cols-3 gap-4">
                @foreach([
                    ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Service Requests'],
                    ['icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z', 'label' => 'Complaints'],
                    ['icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z', 'label' => 'Announcements'],
                ] as $feature)
                <div class="bg-white/10 backdrop-blur rounded-xl p-3 text-center">
                    <svg class="w-6 h-6 text-white mx-auto mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $feature['icon'] }}"/>
                    </svg>
                    <p class="text-white text-xs font-medium leading-tight">{{ $feature['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="relative z-10">
            <p class="text-indigo-300 text-xs">
                &copy; {{ date('Y') }} {{ config('app.name', 'Barangay Connect') }}. All rights reserved.
            </p>
        </div>
    </div>

    {{-- Right Panel: Login Form --}}
    <div class="flex-1 flex flex-col items-center justify-center px-6 py-12 sm:px-12">
        <div class="w-full max-w-md">

            {{-- Mobile Logo --}}
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center
                            text-white font-bold text-lg">B</div>
                <div>
                    <p class="font-bold text-gray-900">Barangay Connect</p>
                    <p class="text-gray-500 text-xs">Community Management System</p>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Welcome back</h2>
                <p class="text-gray-500 mt-1 text-sm">Sign in to your account to continue.</p>
            </div>

            {{-- Session Status --}}
            @if(session('status'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ session('status') }}
            </div>
            @endif

            {{-- Errors --}}
            @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email Address
                    </label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               required autofocus autocomplete="username"
                               placeholder="you@example.com"
                               class="w-full pl-9 pr-4 py-2.5 text-sm rounded-lg border
                                      @error('email') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror
                                      text-gray-900 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                      transition-colors"/>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="text-sm font-medium text-gray-700">
                            Password
                        </label>
                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-xs text-indigo-600 hover:text-indigo-700 font-medium hover:underline">
                            Forgot password?
                        </a>
                        @endif
                    </div>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input :type="showPw ? 'text' : 'password'"
                               id="password" name="password"
                               required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full pl-9 pr-10 py-2.5 text-sm rounded-lg border
                                      @error('password') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror
                                      text-gray-900 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                      transition-colors"/>
                        <button type="button" @click="showPw = !showPw"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!showPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember"
                           class="w-4 h-4 rounded border-gray-300 text-indigo-600
                                  focus:ring-indigo-500 cursor-pointer"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer select-none">
                        Remember me for 30 days
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold
                               rounded-lg bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800
                               text-white transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Sign In
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Don't have an account?
                <a href="{{ route('register') }}"
                   class="text-indigo-600 hover:text-indigo-700 font-medium hover:underline ml-1">
                    Create one now
                </a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
