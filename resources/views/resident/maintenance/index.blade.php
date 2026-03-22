@extends('layouts.app')

@section('title', 'Maintenance Reports')

@section('content')
<div
    x-data="{
        createOpen: false,
        editOpen: false,
        editAction: '',
        editTitle: '',
        editCategory: '',
        editPriority: '',
        editLocation: '',
        editDescription: '',
        openEdit(action, title, category, priority, location, description) {
            this.editAction      = action;
            this.editTitle       = title;
            this.editCategory    = category;
            this.editPriority    = priority;
            this.editLocation    = location;
            this.editDescription = description;
            this.editOpen        = true;
        }
    }"
    @keydown.escape.window="createOpen = false; editOpen = false"
>

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <form method="GET" action="{{ route('resident.maintenance.index') }}"
          class="flex flex-wrap gap-2">
        <select name="status"
                class="py-2 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                       focus:outline-none focus:ring-2 focus:ring-indigo-500"
                onchange="this.form.submit()">
            <option value="">All Statuses</option>
            @foreach($statuses as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
            @endforeach
        </select>

        <select name="category"
                class="py-2 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                       focus:outline-none focus:ring-2 focus:ring-indigo-500"
                onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach($categories as $c)
                <option value="{{ $c }}" @selected(request('category') === $c)>{{ $c }}</option>
            @endforeach
        </select>

        @if(request('status') || request('category'))
        <a href="{{ route('resident.maintenance.index') }}"
           class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600
                  text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Clear
        </a>
        @endif
    </form>

    <button @click="createOpen = true"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg
                   bg-indigo-600 hover:bg-indigo-700 text-white transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Report Issue
    </button>
</div>

{{-- Table --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50">
                    <th class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                    <th class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                    <th class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                    <th class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</th>
                    <th class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reporter</th>
                    <th class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-5 py-3.5 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($tasks as $task)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-5 py-3.5 text-sm font-medium text-gray-900 dark:text-gray-100 max-w-xs">
                        <p class="truncate">{{ $task->title }}</p>
                        @if($task->description)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">
                            {{ Str::limit($task->description, 50) }}
                        </p>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        {{ $task->category }}
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <x-status-badge :status="$task->priority" />
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <x-status-badge :status="$task->status" />
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400 max-w-[10rem]">
                        <span class="truncate block">{{ $task->location ?? '—' }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                        {{ $task->reporter->name ?? '—' }}
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        {{ $task->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                        @if($task->reported_by === auth()->id() && $task->status === 'Reported')
                        <button
                            @click="openEdit(
                                '{{ route('resident.maintenance.update', $task) }}',
                                @js($task->title),
                                @js($task->category),
                                @js($task->priority),
                                @js($task->location ?? ''),
                                @js($task->description ?? '')
                            )"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg
                                   bg-indigo-50 text-indigo-700 hover:bg-indigo-100
                                   dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50
                                   transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </button>
                        @else
                        <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center">
                        <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-sm text-gray-400 dark:text-gray-500">No maintenance reports found.</p>
                        <button @click="createOpen = true"
                                class="mt-3 text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            Report an issue
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tasks->hasPages())
    <div class="px-5 py-4 border-t dark:border-gray-700">
        {{ $tasks->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- ── Report Issue Modal ───────────────────────────────────────────────── --}}
<div x-show="createOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="absolute inset-0 bg-black/50" @click="createOpen = false"></div>

    <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl border dark:border-gray-700 max-h-[90vh] overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop>

        <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Report Maintenance Issue</h3>
            <button @click="createOpen = false"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                           hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('resident.maintenance.store') }}" class="px-6 py-5 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="Brief title of the issue"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                              py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                              placeholder-gray-400 dark:placeholder-gray-500"/>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                   py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select…</option>
                        @foreach($categories as $c)
                            <option value="{{ $c }}" @selected(old('category') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Priority <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                   py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select…</option>
                        @foreach($priorities as $p)
                            <option value="{{ $p }}" @selected(old('priority') === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Location
                </label>
                <input type="text" name="location" value="{{ old('location') }}"
                       placeholder="e.g. Barangay Hall, Street corner, etc."
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                              py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                              placeholder-gray-400 dark:placeholder-gray-500"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea name="description" rows="4" required
                          placeholder="Describe the issue in detail…"
                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                 py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                 placeholder-gray-400 dark:placeholder-gray-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="createOpen = false"
                        class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600
                               text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700
                               text-white transition-colors">
                    Submit Report
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Edit Issue Modal ─────────────────────────────────────────────────── --}}
<div x-show="editOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="absolute inset-0 bg-black/50" @click="editOpen = false"></div>

    <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl border dark:border-gray-700 max-h-[90vh] overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop>

        <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Edit Maintenance Report</h3>
            <button @click="editOpen = false"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                           hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" :action="editAction" class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" x-model="editTitle" required
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                              py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category" x-model="editCategory" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                   py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($categories as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Priority <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" x-model="editPriority" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                   py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($priorities as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Location
                </label>
                <input type="text" name="location" x-model="editLocation"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                              py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea name="description" x-model="editDescription" rows="4" required
                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                 py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                 resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="editOpen = false"
                        class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600
                               text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700
                               text-white transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

</div>{{-- end x-data --}}
@endsection
