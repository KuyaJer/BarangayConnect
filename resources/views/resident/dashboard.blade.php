@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
    <x-stat-card
        label="My Requests"
        :value="$totalRequests"
        color="indigo"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
    />
    <x-stat-card
        label="Pending"
        :value="$pendingRequests"
        color="yellow"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    />
    <x-stat-card
        label="Completed"
        :value="$completedRequests"
        color="green"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    />
    <x-stat-card
        label="My Complaints"
        :value="$totalComplaints"
        color="red"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'
    />
</div>

{{-- Widgets Row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Recent Requests --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700">
        <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">My Recent Requests</h2>
            <a href="{{ route('resident.service-requests.index') }}"
               class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">View all</a>
        </div>
        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($recentRequests as $req)
            <li class="px-5 py-3.5 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $req->subject }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        {{ $req->type }} &middot; {{ $req->created_at->format('M d, Y') }}
                    </p>
                </div>
                <x-status-badge :status="$req->status" />
            </li>
            @empty
            <li class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                You have no service requests yet.
                <a href="{{ route('resident.service-requests.index') }}"
                   class="block mt-2 text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                    Submit your first request
                </a>
            </li>
            @endforelse
        </ul>
    </div>

    {{-- Announcements Widget --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700">
        <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Announcements</h2>
            <a href="{{ route('resident.announcements') }}"
               class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">See all</a>
        </div>
        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($announcements->take(5) as $ann)
            <li class="px-5 py-4">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100 leading-snug line-clamp-1">
                        {{ $ann->title }}
                    </p>
                    <x-status-badge :status="$ann->priority" />
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-1">{{ $ann->content }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    {{ $ann->created_at->diffForHumans() }}
                </p>
            </li>
            @empty
            <li class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                No announcements at this time.
            </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
