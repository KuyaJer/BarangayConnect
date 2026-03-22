@extends('layouts.app')

@section('title', 'Announcements')

@section('content')

{{-- Search Bar --}}
<div class="mb-5">
    <form method="GET" action="{{ route('resident.announcements') }}" class="flex gap-2">
        <div class="relative flex-1 max-w-md">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search announcements…"
                   class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                          placeholder-gray-400 dark:placeholder-gray-500
                          focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
        </div>
        <button type="submit"
                class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700
                       text-white transition-colors">
            Search
        </button>
        @if(request('search'))
        <a href="{{ route('resident.announcements') }}"
           class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600
                  text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Clear
        </a>
        @endif
    </form>
</div>

{{-- Announcements Grid --}}
@if($announcements->isEmpty())
<div class="flex flex-col items-center justify-center py-20 text-center">
    <svg class="w-14 h-14 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
    </svg>
    <p class="text-gray-500 dark:text-gray-400 font-medium">No announcements found.</p>
    @if(request('search'))
    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
        Try a different search term.
    </p>
    @endif
</div>
@else
<div class="space-y-4">
    @foreach($announcements as $announcement)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5
                @if($announcement->priority === 'Urgent') border-l-4 border-l-red-500 @else border-l-4 border-l-gray-300 dark:border-l-gray-600 @endif">
        <div class="flex items-start justify-between gap-4 mb-3">
            <div class="flex-1 min-w-0">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white leading-snug">
                    {{ $announcement->title }}
                </h3>
            </div>
            <div class="flex-shrink-0">
                @if($announcement->priority === 'Urgent')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Urgent
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        Normal
                    </span>
                @endif
            </div>
        </div>

        <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 mb-4">
            <p class="whitespace-pre-line leading-relaxed">{{ $announcement->content }}</p>
        </div>

        <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500 pt-3 border-t dark:border-gray-700">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>{{ $announcement->author->name ?? 'Barangay Office' }}</span>
            <span>&middot;</span>
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>{{ $announcement->created_at->format('F j, Y') }}</span>
            <span>&middot;</span>
            <span>{{ $announcement->created_at->diffForHumans() }}</span>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
@if($announcements->hasPages())
<div class="mt-6">
    {{ $announcements->withQueryString()->links() }}
</div>
@endif
@endif

@endsection
