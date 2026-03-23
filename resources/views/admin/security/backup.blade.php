@extends('layouts.app')

@section('title', 'Data Backup & Recovery')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 px-5 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-base font-semibold text-gray-900 dark:text-white">Data Backup &amp; Recovery</h1>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                Create, download, and restore database backups.
            </p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
            </svg>
            Backup
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

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl px-4 py-3">
        <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-0.5">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Left panel: actions --}}
        <div class="space-y-4">

            {{-- Create backup --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Create Backup</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                    Dumps the current database to a .sql file and stores it on the server.
                </p>
                <form method="POST" action="{{ route('admin.security.backup.create') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg
                                   bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Create Backup Now
                    </button>
                </form>
            </div>

            {{-- Restore backup --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5"
                 x-data="{ confirm: false }">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Restore Database</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                    Upload a <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">.sql</code> file to restore the database.
                    <span class="text-red-500 font-medium">This will overwrite current data.</span>
                </p>

                <form method="POST" action="{{ route('admin.security.backup.restore') }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div x-show="!confirm">
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">SQL File</label>
                            <input type="file" name="sql_file" accept=".sql,.txt"
                                   class="w-full text-xs text-gray-700 dark:text-gray-300
                                          file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                          file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700
                                          dark:file:bg-indigo-900/30 dark:file:text-indigo-300
                                          hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50"/>
                        </div>
                        <button type="button" @click="confirm = true"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg
                                       bg-red-600 hover:bg-red-700 text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Restore Database
                        </button>
                    </div>

                    {{-- Confirm step --}}
                    <div x-show="confirm" x-transition>
                        <p class="text-sm font-medium text-red-600 dark:text-red-400 mb-3">
                            Are you sure? This will replace all current data with the backup file.
                        </p>
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="flex-1 px-3 py-2 text-sm font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors">
                                Yes, Restore
                            </button>
                            <button type="button" @click="confirm = false"
                                    class="flex-1 px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600
                                           text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        {{-- Right panel: backup list --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">

                <div class="px-5 py-3 border-b dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Stored Backups
                        <span class="ml-1.5 text-xs font-normal text-gray-400 dark:text-gray-500">({{ $files->count() }})</span>
                    </h2>
                    <span class="text-xs text-gray-400 dark:text-gray-500">Newest first</span>
                </div>

                @if($files->isEmpty())
                <div class="px-5 py-16 text-center">
                    <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                    <p class="text-sm text-gray-400 dark:text-gray-500">No backups yet. Create one above.</p>
                </div>
                @else
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($files as $file)
                    <li class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">

                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $file['filename'] }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                {{ $file['modified']->format('M d, Y g:i A') }} &middot; {{ $file['size'] }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            {{-- Download --}}
                            <a href="{{ route('admin.security.backup.download', $file['filename']) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                      bg-indigo-50 text-indigo-700 hover:bg-indigo-100
                                      dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>

                            {{-- Delete --}}
                            <form method="POST"
                                  action="{{ route('admin.security.backup.destroy', $file['filename']) }}"
                                  onsubmit="return confirm('Delete this backup? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                               bg-red-50 text-red-700 hover:bg-red-100
                                               dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>

                    </li>
                    @endforeach
                </ul>
                @endif

            </div>
        </div>

    </div>

</div>
@endsection
