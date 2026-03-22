@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 px-5 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-base font-semibold text-gray-900 dark:text-white">Roles &amp; Permissions</h1>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                Toggle permissions for each role. Admins always have full access.
            </p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            RBAC
        </span>
    </div>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
         class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl px-4 py-3 flex items-center gap-3">
        <svg class="w-4 h-4 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Matrix form --}}
    <form method="POST" action="{{ route('admin.security.rbac.update') }}">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">

            {{-- Legend --}}
            <div class="px-5 py-3 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 flex items-center gap-6 text-xs text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-4 h-4 rounded border-2 border-indigo-500 bg-indigo-500"></span> Allowed
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-4 h-4 rounded border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800"></span> Denied
                </span>
                <span class="flex items-center gap-1.5 ml-auto">
                    <span class="inline-block w-4 h-4 rounded bg-purple-100 dark:bg-purple-900/30 border border-purple-300 dark:border-purple-700"></span>
                    Admin — always full access
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-5 py-3 text-left w-1/2">Permission</th>
                            <th class="px-5 py-3 text-center w-1/6">
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span> Admin
                                </span>
                            </th>
                            <th class="px-5 py-3 text-center w-1/6">
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Staff
                                </span>
                            </th>
                            <th class="px-5 py-3 text-center w-1/6">
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span> Resident
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">

                        @foreach($groups as $groupName => $perms)
                        {{-- Group header row --}}
                        <tr class="bg-gray-50/70 dark:bg-gray-700/20">
                            <td colspan="4" class="px-5 py-2">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ $groupName }}
                                </span>
                            </td>
                        </tr>

                        @foreach($perms as $perm)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-5 py-3">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $perm->display_name }}</p>
                                @if($perm->description)
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $perm->description }}</p>
                                @endif
                            </td>

                            {{-- Admin — always on, disabled --}}
                            <td class="px-5 py-3 text-center">
                                <input type="checkbox" checked disabled
                                       class="w-4 h-4 rounded accent-purple-500 opacity-60 cursor-not-allowed"/>
                            </td>

                            {{-- Staff --}}
                            <td class="px-5 py-3 text-center">
                                <input type="checkbox"
                                       name="permissions[staff][{{ $perm->name }}]"
                                       value="1"
                                       {{ isset($assigned['staff'][$perm->name]) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded accent-indigo-600 cursor-pointer"/>
                            </td>

                            {{-- Resident --}}
                            <td class="px-5 py-3 text-center">
                                <input type="checkbox"
                                       name="permissions[resident][{{ $perm->name }}]"
                                       value="1"
                                       {{ isset($assigned['resident'][$perm->name]) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded accent-indigo-600 cursor-pointer"/>
                            </td>
                        </tr>
                        @endforeach

                        @endforeach

                    </tbody>
                </table>
            </div>

            {{-- Save button --}}
            <div class="px-5 py-4 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 flex justify-end">
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">
                    Save Permissions
                </button>
            </div>

        </div>
    </form>

</div>
@endsection
