@extends('layouts.app')

@section('title', 'My Service Requests')

@section('content')
<div
    x-data="{
        createOpen: false,
        editOpen: false,
        rateOpen: false,
        editAction: '',
        rateAction: '',
        editSubject: '',
        editType: '',
        editDescription: '',
        rating: 0,
        hoverRating: 0,
        openEdit(action, subject, type, description) {
            this.editAction      = action;
            this.editSubject     = subject;
            this.editType        = type;
            this.editDescription = description;
            this.editOpen        = true;
        },
        openRate(action) {
            this.rateAction = action;
            this.rating     = 0;
            this.rateOpen   = true;
        }
    }"
    @keydown.escape.window="createOpen = false; editOpen = false; rateOpen = false"
>

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <form method="GET" action="{{ route('resident.service-requests.index') }}"
          class="flex flex-col sm:flex-row gap-2">
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
        @if(request('status'))
        <a href="{{ route('resident.service-requests.index') }}"
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
        New Request
    </button>
</div>

{{-- Table --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50">
                    <th class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                    <th class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                    <th class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3.5 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($requests as $request)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-5 py-3.5 text-sm font-medium text-gray-900 dark:text-gray-100 max-w-xs">
                        <p class="truncate">{{ $request->subject }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate mt-0.5">{{ Str::limit($request->description, 60) }}</p>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        {{ $request->type }}
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        {{ $request->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <x-status-badge :status="$request->status" />
                    </td>
                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-2">
                            @if($request->status === 'Pending')
                            <button
                                @click="openEdit(
                                    '{{ route('resident.service-requests.update', $request) }}',
                                    @js($request->subject),
                                    @js($request->type),
                                    @js($request->description)
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
                            @endif

                            @if($request->status === 'Completed' && !$request->feedback)
                            <button
                                @click="openRate('{{ route('resident.service-requests.feedback', $request) }}')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg
                                       bg-yellow-50 text-yellow-700 hover:bg-yellow-100
                                       dark:bg-yellow-900/30 dark:text-yellow-300 dark:hover:bg-yellow-900/50
                                       transition-colors">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Rate
                            </button>
                            @endif

                            @if($request->status === 'Completed' && $request->feedback)
                            <span class="inline-flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500">
                                <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Rated {{ $request->feedback->rating }}/5
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center">
                        <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm text-gray-400 dark:text-gray-500">You have no service requests yet.</p>
                        <button @click="createOpen = true"
                                class="mt-3 text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            Submit your first request
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
    <div class="px-5 py-4 border-t dark:border-gray-700">
        {{ $requests->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- ── Create Modal ─────────────────────────────────────────────────────── --}}
<div x-show="createOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="absolute inset-0 bg-black/50" @click="createOpen = false"></div>

    <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl border dark:border-gray-700"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop>

        <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">New Service Request</h3>
            <button @click="createOpen = false"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                           hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('resident.service-requests.store') }}" class="px-6 py-5 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Request Type <span class="text-red-500">*</span>
                </label>
                <select name="type" required
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                               py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select type…</option>
                    @foreach($types as $t)
                        <option value="{{ $t }}" @selected(old('type') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Subject <span class="text-red-500">*</span>
                </label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                       placeholder="Brief description of your request"
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
                          placeholder="Provide details about your request…"
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
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Edit Modal ───────────────────────────────────────────────────────── --}}
<div x-show="editOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="absolute inset-0 bg-black/50" @click="editOpen = false"></div>

    <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl border dark:border-gray-700"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop>

        <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Edit Request</h3>
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
                    Request Type <span class="text-red-500">*</span>
                </label>
                <select name="type" x-model="editType" required
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                               py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($types as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Subject <span class="text-red-500">*</span>
                </label>
                <input type="text" name="subject" x-model="editSubject" required
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

{{-- ── Rate / Feedback Modal ────────────────────────────────────────────── --}}
<div x-show="rateOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="absolute inset-0 bg-black/50" @click="rateOpen = false"></div>

    <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl border dark:border-gray-700"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop>

        <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Rate This Service</h3>
            <button @click="rateOpen = false"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                           hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" :action="rateAction" class="px-6 py-5 space-y-5">
            @csrf

            {{-- Star Rating --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Your Rating <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-1">
                    @for($star = 1; $star <= 5; $star++)
                    <label class="cursor-pointer">
                        <input type="radio" name="rating" value="{{ $star }}" x-model="rating" class="sr-only" required/>
                        <svg class="w-9 h-9 transition-colors"
                             :class="(hoverRating || rating) >= {{ $star }} ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'"
                             @mouseenter="hoverRating = {{ $star }}"
                             @mouseleave="hoverRating = 0"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </label>
                    @endfor
                </div>
                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400"
                   x-text="rating ? rating + ' star' + (rating > 1 ? 's' : '') + ' selected' : 'Click a star to rate'"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Comment <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <textarea name="comment" rows="3"
                          placeholder="Share your experience…"
                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                 py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                 placeholder-gray-400 dark:placeholder-gray-500 resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="rateOpen = false"
                        class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600
                               text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" :disabled="!rating"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700
                               text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Submit Feedback
                </button>
            </div>
        </form>
    </div>
</div>

</div>{{-- end x-data --}}
@endsection
