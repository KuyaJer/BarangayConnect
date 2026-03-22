@props(['label', 'value', 'color' => 'indigo', 'icon' => null])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5 flex items-center gap-4">
    @if($icon)
    <div class="w-12 h-12 rounded-xl bg-{{ $color }}-100 dark:bg-{{ $color }}-900/40 flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 text-{{ $color }}-600 dark:text-{{ $color }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $icon !!}
        </svg>
    </div>
    @endif
    <div>
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $label }}</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $value }}</p>
    </div>
</div>
