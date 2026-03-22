@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="space-y-4">

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 px-5 py-4">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

            {{-- Search --}}
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search description…"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                              py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>

            {{-- Action --}}
            <div>
                <select name="action"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                               py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $action)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- User --}}
            <div>
                <select name="user_id"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                               py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') === $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->role }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Subject type --}}
            <div>
                <select name="subject_type"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                               py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s }}" {{ request('subject_type') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date from --}}
            <div>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                              py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>

            {{-- Date to --}}
            <div>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                              py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-lg
                               bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">
                    Filter
                </button>
                <a href="{{ route('admin.activity-logs.index') }}"
                   class="flex-1 px-4 py-2 text-sm font-medium rounded-lg text-center
                          border border-gray-300 dark:border-gray-600
                          text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">

        <div class="flex items-center justify-between px-5 py-4 border-b dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ $logs->total() }} {{ Str::plural('entry', $logs->total()) }}
            </h2>
            <span class="text-xs text-gray-400 dark:text-gray-500">Showing latest first</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3 text-left">Timestamp</th>
                        <th class="px-5 py-3 text-left">User</th>
                        <th class="px-5 py-3 text-left">Action</th>
                        <th class="px-5 py-3 text-left">Description</th>
                        <th class="px-5 py-3 text-left">Subject</th>
                        <th class="px-5 py-3 text-left">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">

                        {{-- Timestamp --}}
                        <td class="px-5 py-3 whitespace-nowrap">
                            <p class="text-xs font-medium text-gray-900 dark:text-gray-100">
                                {{ $log->created_at->format('M d, Y') }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $log->created_at->format('g:i A') }}
                            </p>
                        </td>

                        {{-- User --}}
                        <td class="px-5 py-3">
                            @if($log->user)
                                <p class="text-xs font-medium text-gray-900 dark:text-gray-100 truncate max-w-[120px]">
                                    {{ $log->user->name }}
                                </p>
                                <span class="inline-block mt-0.5 text-xs px-1.5 py-0.5 rounded
                                    {{ $log->user->role === 'admin'
                                        ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'
                                        : ($log->user->role === 'staff'
                                            ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
                                            : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300') }}">
                                    {{ $log->user->role }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">Deleted user</span>
                            @endif
                        </td>

                        {{-- Action badge --}}
                        <td class="px-5 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $log->actionColor() }}">
                                {{ $log->actionLabel() }}
                            </span>
                        </td>

                        {{-- Description --}}
                        <td class="px-5 py-3 max-w-xs">
                            <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ $log->description }}
                            </p>
                            @if($log->properties)
                                <div class="mt-1 flex gap-2 flex-wrap">
                                    @foreach($log->properties as $key => $val)
                                        <span class="text-xs text-gray-400 dark:text-gray-500">
                                            <span class="font-medium">{{ str_replace('_', ' ', $key) }}:</span> {{ $val }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>

                        {{-- Subject --}}
                        <td class="px-5 py-3 whitespace-nowrap">
                            @if($log->subject_type)
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $log->subject_type }}
                                </span>
                            @else
                                <span class="text-xs text-gray-300 dark:text-gray-600">—</span>
                            @endif
                        </td>

                        {{-- IP --}}
                        <td class="px-5 py-3 whitespace-nowrap">
                            <span class="text-xs font-mono text-gray-400 dark:text-gray-500">
                                {{ $log->ip_address ?? '—' }}
                            </span>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-3"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm text-gray-400 dark:text-gray-500">No activity logs found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-5 py-4 border-t dark:border-gray-700">
            {{ $logs->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
