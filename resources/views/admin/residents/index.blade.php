@extends('layouts.app')

@section('title', 'Residents')

@section('content')
<div
    x-data="{
        addOpen: false,
        editOpen: false,
        editData: {},
        openEdit(resident) {
            this.editData = resident;
            this.editOpen = true;
        }
    }"
    @keydown.escape.window="addOpen = false; editOpen = false"
>

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <form method="GET" action="{{ route('admin.residents.index') }}"
          class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search residents…"
                   class="pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                          placeholder-gray-400 dark:placeholder-gray-500
                          focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64"/>
        </div>

        <select name="purok"
                class="py-2 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                       focus:outline-none focus:ring-2 focus:ring-indigo-500"
                onchange="this.form.submit()">
            <option value="">All Puroks</option>
            @foreach($puroks as $p)
                <option value="{{ $p }}" @selected(request('purok') === $p)>{{ $p }}</option>
            @endforeach
        </select>

        <button type="submit"
                class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700
                       text-white transition-colors">
            Search
        </button>
        @if(request('search') || request('purok'))
        <a href="{{ route('admin.residents.index') }}"
           class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600
                  text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Clear
        </a>
        @endif
    </form>

    <button @click="addOpen = true"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg
                   bg-indigo-600 hover:bg-indigo-700 text-white transition-colors flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Resident
    </button>
</div>

{{-- Resident Cards Grid --}}
@if($residents->isEmpty())
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 py-16 text-center">
    <svg class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    <p class="text-gray-400 dark:text-gray-500 text-sm">No residents found.</p>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5 mb-5">
    @foreach($residents as $resident)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5
                flex flex-col">
        {{-- Avatar + Name --}}
        <div class="flex flex-col items-center text-center mb-4">
            <div class="w-14 h-14 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center
                        justify-center text-indigo-700 dark:text-indigo-300 text-xl font-bold mb-3">
                {{ $resident->initials }}
            </div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">
                {{ $resident->full_name }}
            </h3>
            @if($resident->resident_id_number)
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                ID: {{ $resident->resident_id_number }}
            </p>
            @endif
            @if($resident->verified)
            <span class="mt-1.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                         bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Verified
            </span>
            @endif
        </div>

        {{-- Details --}}
        <div class="space-y-1.5 text-xs text-gray-500 dark:text-gray-400 flex-1">
            @if($resident->address)
            <div class="flex items-start gap-1.5">
                <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="line-clamp-2">{{ $resident->address }}</span>
            </div>
            @endif
            @if($resident->purok)
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Purok {{ $resident->purok }}
            </div>
            @endif
            @if($resident->contact_number)
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                {{ $resident->contact_number }}
            </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2 mt-4 pt-4 border-t dark:border-gray-700">
            <button
                @click="openEdit(@js(['id' => $resident->id, 'first_name' => $resident->first_name, 'last_name' => $resident->last_name, 'address' => $resident->address, 'purok' => $resident->purok, 'contact_number' => $resident->contact_number, 'email' => $resident->email, 'household_id' => $resident->household_id, 'resident_id_number' => $resident->resident_id_number, 'verified' => $resident->verified]))"
                class="flex-1 py-1.5 text-xs font-medium rounded-lg border border-indigo-200 dark:border-indigo-700
                       text-indigo-700 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20
                       transition-colors text-center">
                Edit
            </button>

            <form method="POST" action="{{ route('admin.residents.destroy', $resident) }}"
                  onsubmit="return confirm('Delete this resident record? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="py-1.5 px-3 text-xs font-medium rounded-lg border border-red-200 dark:border-red-800
                               text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20
                               transition-colors">
                    Delete
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
@if($residents->hasPages())
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 px-5 py-4">
    {{ $residents->withQueryString()->links() }}
</div>
@endif
@endif

{{-- ── Add Resident Modal ──────────────────────────────────────────────────── --}}
<div x-show="addOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="absolute inset-0 bg-black/50" @click="addOpen = false"></div>

    <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl
                border dark:border-gray-700 my-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop>

        <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Add Resident</h3>
            <button @click="addOpen = false"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                           hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.residents.store') }}" class="px-6 py-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                    @error('first_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                    @error('last_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Purok</label>
                    <select name="purok"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                   py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Purok</option>
                        @foreach($puroks as $p)
                            <option value="{{ $p }}" @selected(old('purok') === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Household ID</label>
                    <input type="text" name="household_id" value="{{ old('household_id') }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Resident ID Number</label>
                    <input type="text" name="resident_id_number" value="{{ old('resident_id_number') }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div class="col-span-2">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" name="verified" value="1"
                               @checked(old('verified'))
                               class="w-4 h-4 rounded border-gray-300 dark:border-gray-600
                                      text-indigo-600 focus:ring-indigo-500"/>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Mark as Verified</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" @click="addOpen = false"
                        class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600
                               text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700
                               text-white transition-colors">
                    Add Resident
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Edit Resident Modal ──────────────────────────────────────────────────── --}}
<div x-show="editOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="absolute inset-0 bg-black/50" @click="editOpen = false"></div>

    <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl
                border dark:border-gray-700 my-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop>

        <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Edit Resident</h3>
            <button @click="editOpen = false"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                           hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST"
              :action="`{{ url('admin/residents') }}/${editData.id}`"
              class="px-6 py-5">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" :value="editData.first_name" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" :value="editData.last_name" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                    <input type="text" name="address" :value="editData.address"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Purok</label>
                    <select name="purok"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                   py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Purok</option>
                        @foreach($puroks as $p)
                            <option value="{{ $p }}" :selected="editData.purok === '{{ $p }}'">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contact Number</label>
                    <input type="text" name="contact_number" :value="editData.contact_number"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                    <input type="email" name="email" :value="editData.email"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Household ID</label>
                    <input type="text" name="household_id" :value="editData.household_id"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Resident ID Number</label>
                    <input type="text" name="resident_id_number" :value="editData.resident_id_number"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div class="col-span-2">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" name="verified" value="1"
                               :checked="editData.verified"
                               class="w-4 h-4 rounded border-gray-300 dark:border-gray-600
                                      text-indigo-600 focus:ring-indigo-500"/>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Mark as Verified</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
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
