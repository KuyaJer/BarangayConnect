<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account &mdash; {{ config('app.name', 'Barangay Connect') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900"
      x-data="{ showPw: false, showConfirm: false }">

<div class="min-h-screen flex">

    {{-- Left Panel: Branding --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-2/5 flex-col justify-between
                bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-700 p-12 relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white/5"></div>
            <div class="absolute -bottom-32 -right-32 w-[30rem] h-[30rem] rounded-full bg-white/5"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 rounded-full bg-white/5"></div>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center text-white font-bold text-xl shadow-lg">B</div>
                <div>
                    <p class="text-white font-bold text-lg leading-none">Barangay Connect</p>
                    <p class="text-indigo-200 text-xs mt-0.5">Community Management System</p>
                </div>
            </div>
        </div>
        <div class="relative z-10 flex-1 flex flex-col justify-center">
            <h1 class="text-4xl font-bold text-white leading-tight mb-4">Join your<br/>community<br/>today.</h1>
            <p class="text-indigo-200 text-base leading-relaxed max-w-sm">
                Register as a resident to access barangay services, submit requests,
                file complaints, and stay informed about your community.
            </p>
            <div class="mt-10 space-y-3">
                @foreach(['Submit service requests online','Track your requests in real time','Receive instant notifications','Stay updated with announcements'] as $benefit)
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-indigo-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-indigo-100 text-sm">{{ $benefit }}</p>
                </div>
                @endforeach
            </div>
        </div>
        <div class="relative z-10">
            <p class="text-indigo-300 text-xs">&copy; {{ date('Y') }} {{ config('app.name', 'Barangay Connect') }}. All rights reserved.</p>
        </div>
    </div>

    {{-- Right Panel: Register Form --}}
    <div class="flex-1 flex flex-col items-center justify-center px-6 py-12 sm:px-12 overflow-y-auto">
        <div class="w-full max-w-md">

            {{-- Mobile Logo --}}
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-lg">B</div>
                <div>
                    <p class="font-bold text-gray-900">Barangay Connect</p>
                    <p class="text-gray-500 text-xs">Community Management System</p>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Create your account</h2>
                <p class="text-gray-500 mt-1 text-sm">Register as a resident to get started.</p>
            </div>

            @if($errors->any())
            <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                {{-- First Name + Middle Name --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name"
                               value="{{ old('first_name') }}" required autofocus
                               placeholder="Juan"
                               class="w-full px-3 py-2.5 text-sm rounded-lg border
                                      @error('first_name') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror
                                      text-gray-900 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"/>
                        @error('first_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Middle Name
                        </label>
                        <input type="text" id="middle_name" name="middle_name"
                               value="{{ old('middle_name') }}"
                               placeholder="Santos"
                               class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 bg-white
                                      text-gray-900 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"/>
                    </div>
                </div>

                {{-- Surname + Suffix --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="surname" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Surname <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="surname" name="surname"
                               value="{{ old('surname') }}" required
                               placeholder="dela Cruz"
                               class="w-full px-3 py-2.5 text-sm rounded-lg border
                                      @error('surname') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror
                                      text-gray-900 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"/>
                        @error('surname')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="suffix" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Suffix
                        </label>
                        <input type="text" id="suffix" name="suffix"
                               value="{{ old('suffix') }}"
                               placeholder="Jr., Sr., III…"
                               class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 bg-white
                                      text-gray-900 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"/>
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               required autocomplete="username"
                               placeholder="you@example.com"
                               class="w-full pl-9 pr-4 py-2.5 text-sm rounded-lg border
                                      @error('email') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror
                                      text-gray-900 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"/>
                    </div>
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Contact Number --}}
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Contact Number
                        <span class="text-gray-400 font-normal text-xs">(optional)</span>
                    </label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <input type="tel" id="contact_number" name="contact_number"
                               value="{{ old('contact_number') }}"
                               placeholder="09XXXXXXXXX"
                               maxlength="11"
                               x-on:input="
                                   let v = $event.target.value.replace(/\D/g,'');
                                   if(v.length>0 && v[0]!=='0') v='0'+v;
                                   if(v.length>1 && v[1]!=='9') v=v[0]+'9'+v.slice(1);
                                   if(v.length>11) v=v.slice(0,11);
                                   $event.target.value=v;
                               "
                               class="w-full pl-9 pr-4 py-2.5 text-sm rounded-lg border
                                      @error('contact_number') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror
                                      text-gray-900 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"/>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Must start with 09, exactly 11 digits.</p>
                    @error('contact_number')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Birthdate + Gender --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="birthdate" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Birthdate
                            <span class="text-gray-400 font-normal text-xs">(optional)</span>
                        </label>
                        <input type="date" id="birthdate" name="birthdate"
                               value="{{ old('birthdate') }}"
                               class="w-full px-3 py-2.5 text-sm rounded-lg border
                                      @error('birthdate') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror
                                      text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"/>
                        @error('birthdate')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Gender
                            <span class="text-gray-400 font-normal text-xs">(optional)</span>
                        </label>
                        <select id="gender" name="gender"
                                class="w-full px-3 py-2.5 text-sm rounded-lg border
                                       @error('gender') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror
                                       text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                            <option value="">Select…</option>
                            @foreach(['Male','Female','Non-binary','Prefer not to say'] as $g)
                                <option value="{{ $g }}" {{ old('gender') === $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                        @error('gender')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Civil Status --}}
                <div>
                    <label for="civil_status" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Civil Status
                        <span class="text-gray-400 font-normal text-xs">(optional)</span>
                    </label>
                    <select id="civil_status" name="civil_status"
                            class="w-full px-3 py-2.5 text-sm rounded-lg border
                                   @error('civil_status') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror
                                   text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                        <option value="">Select status…</option>
                        @foreach(['Single','Married','Widowed','Separated','Divorced'] as $cs)
                            <option value="{{ $cs }}" {{ old('civil_status') === $cs ? 'selected' : '' }}>{{ $cs }}</option>
                        @endforeach
                    </select>
                    @error('civil_status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Current Place --}}
                <div>
                    <label for="current_place" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Current Place
                        <span class="text-gray-400 font-normal text-xs">(optional)</span>
                    </label>
                    <input type="text" id="current_place" name="current_place"
                           value="{{ old('current_place') }}"
                           placeholder="Street, Barangay, City/Municipality"
                           class="w-full px-3 py-2.5 text-sm rounded-lg border
                                  @error('current_place') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror
                                  text-gray-900 placeholder-gray-400
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"/>
                    @error('current_place')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input :type="showPw ? 'text' : 'password'"
                               id="password" name="password"
                               required autocomplete="new-password"
                               placeholder="Minimum 6 characters"
                               class="w-full pl-9 pr-10 py-2.5 text-sm rounded-lg border
                                      @error('password') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror
                                      text-gray-900 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"/>
                        <button type="button" @click="showPw=!showPw"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!showPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <input :type="showConfirm ? 'text' : 'password'"
                               id="password_confirmation" name="password_confirmation"
                               required autocomplete="new-password"
                               placeholder="Re-enter your password"
                               class="w-full pl-9 pr-10 py-2.5 text-sm rounded-lg border border-gray-300 bg-white
                                      text-gray-900 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"/>
                        <button type="button" @click="showConfirm=!showConfirm"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <p class="text-xs text-gray-400 leading-relaxed pt-1">
                    By creating an account you agree to the barangay's terms of service and privacy policy.
                    Your account will be registered as a <strong class="text-gray-600 font-medium">Resident</strong>.
                </p>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold
                               rounded-lg bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800
                               text-white transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Create Account
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-medium hover:underline ml-1">Sign in here</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
