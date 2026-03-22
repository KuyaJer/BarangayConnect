@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Notifications List --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $notifications->total() }} notification{{ $notifications->total() !== 1 ? 's' : '' }}
            </p>
            @if($notifications->where('read', false)->count() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg
                               bg-indigo-50 text-indigo-700 hover:bg-indigo-100
                               dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50
                               transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Mark All Read
                </button>
            </form>
            @endif
        </div>

        {{-- Notifications --}}
        @forelse($notifications as $notification)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-4
                    {{ !$notification->read ? 'border-l-4 border-l-indigo-500' : '' }}">
            <div class="flex items-start gap-3">
                {{-- Read indicator --}}
                <div class="mt-1.5 flex-shrink-0">
                    @if(!$notification->read)
                        <span class="block w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                    @else
                        <span class="block w-2.5 h-2.5 rounded-full bg-gray-200 dark:bg-gray-600"></span>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <p class="text-sm font-semibold leading-snug
                                  {{ !$notification->read ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-900 dark:text-white' }}">
                            {{ $notification->title }}
                        </p>
                        <span class="flex-shrink-0 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-3">
                        {{ $notification->message }}
                    </p>

                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="text-xs text-gray-400 dark:text-gray-500">
                            {{ $notification->created_at->format('M d, Y \a\t g:i A') }}
                        </span>

                        {{-- View link — marks read and redirects to the relevant page --}}
                        @if($notification->reference_type)
                        <a href="{{ route('notifications.view', $notification) }}"
                           class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            View
                        </a>
                        @endif

                        @if(!$notification->read)
                        <form method="POST" action="{{ route('notifications.mark-read', $notification) }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="text-xs text-gray-500 dark:text-gray-400 hover:underline">
                                Mark as Read
                            </button>
                        </form>
                        @else
                        <span class="inline-flex items-center gap-1 text-xs text-green-600 dark:text-green-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Read
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 px-6 py-16 text-center">
            <svg class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">No notifications yet.</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">You're all caught up!</p>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($notifications->hasPages())
        <div class="mt-2">
            {{ $notifications->withQueryString()->links() }}
        </div>
        @endif
    </div>

    {{-- Right: Announcements Sidebar --}}
    <div class="space-y-4">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 px-1">Latest Announcements</h2>

        @forelse($announcements as $ann)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-4">
            <div class="flex items-start justify-between gap-2 mb-2">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 line-clamp-2 leading-snug">
                    {{ $ann->title }}
                </p>
                @if($ann->priority === 'Urgent')
                    <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Urgent</span>
                @else
                    <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Normal</span>
                @endif
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-3 mb-2 leading-relaxed">{{ $ann->content }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $ann->created_at->diffForHumans() }}</p>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 px-4 py-8 text-center">
            <p class="text-sm text-gray-400 dark:text-gray-500">No announcements yet.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
