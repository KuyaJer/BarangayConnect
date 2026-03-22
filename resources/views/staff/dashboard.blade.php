@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
    <x-stat-card
        label="Total Requests"
        :value="$totalRequests"
        color="indigo"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
    />
    <x-stat-card
        label="Pending Requests"
        :value="$pendingRequests"
        color="yellow"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    />
    <x-stat-card
        label="Completed Requests"
        :value="$completedRequests"
        color="green"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    />
    <x-stat-card
        label="Total Residents"
        :value="$totalResidents"
        color="blue"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'
    />
</div>

{{-- Recent Requests & Announcements --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Recent Requests Table --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700">
        <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Recent Service Requests</h2>
            <a href="{{ route('staff.service-requests.index') }}"
               class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Requester</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentRequests as $req)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-5 py-3 text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate">
                            {{ $req->subject }}
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $req->type }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $req->user->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <x-status-badge :status="$req->status" />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                            No recent requests.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Announcements --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700">
        <div class="px-5 py-4 border-b dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Latest Announcements</h2>
        </div>
        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($announcements as $ann)
            <li class="px-5 py-4">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100 leading-snug line-clamp-1">
                        {{ $ann->title }}
                    </p>
                    <x-status-badge :status="$ann->priority" />
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-1.5">{{ $ann->content }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    By {{ $ann->author->name ?? '—' }} &middot;
                    {{ $ann->created_at->diffForHumans() }}
                </p>
            </li>
            @empty
            <li class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                No announcements yet.
            </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
