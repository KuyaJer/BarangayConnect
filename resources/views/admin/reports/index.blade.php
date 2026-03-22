@extends('layouts.app')

@section('title', 'Reports')

@section('content')

{{-- Print button --}}
<div class="flex justify-end mb-5 print:hidden">
    <button onclick="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg
                   border border-gray-300 dark:border-gray-600
                   text-gray-700 dark:text-gray-300
                   hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Print Report
    </button>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    <x-stat-card
        label="Total Requests"
        :value="$totalRequests"
        color="indigo"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
    />
    <x-stat-card
        label="Total Complaints"
        :value="$totalComplaints"
        color="red"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"/>'
    />
    <x-stat-card
        label="Total Residents"
        :value="$totalResidents"
        color="blue"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'
    />
    <x-stat-card
        label="Maintenance Tasks"
        :value="$totalMaintenance"
        color="yellow"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'
    />
</div>

{{-- Charts Row 1: Monthly + By Type --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Monthly Service Requests</h2>
        <div class="relative h-64">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Requests by Type</h2>
        <div class="relative h-64 flex items-center justify-center">
            <canvas id="typeChart"></canvas>
        </div>
    </div>
</div>

{{-- Charts Row 2: By Category + Feedback Ratings --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Maintenance by Category</h2>
        <div class="relative h-64">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Feedback Ratings (1–5)</h2>
        <div class="relative h-64">
            <canvas id="ratingsChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor  = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const labelColor = isDark ? '#9ca3af' : '#6b7280';

    const palette = ['#6366f1','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6'];

    // ── Monthly Bar Chart ─────────────────────────────────────────────────────
    const monthlyData = @json($monthlyData);
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: monthlyData.map(d => {
                const date = new Date(d.year, d.month - 1);
                return date.toLocaleString('default', { month: 'short', year: '2-digit' });
            }),
            datasets: [{
                label: 'Requests',
                data: monthlyData.map(d => d.count),
                backgroundColor: 'rgba(99,102,241,0.75)',
                borderColor: 'rgba(99,102,241,1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: labelColor, font: { size: 11 } }, grid: { color: gridColor } },
                y: { beginAtZero: true, ticks: { color: labelColor, font: { size: 11 }, stepSize: 1 }, grid: { color: gridColor } }
            }
        }
    });

    // ── By-Type Pie Chart ─────────────────────────────────────────────────────
    const byType = @json($byType);
    new Chart(document.getElementById('typeChart'), {
        type: 'pie',
        data: {
            labels: byType.map(d => d.type),
            datasets: [{
                data: byType.map(d => d.count),
                backgroundColor: palette.slice(0, byType.length),
                borderWidth: 2,
                borderColor: isDark ? '#1f2937' : '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: labelColor, font: { size: 11 }, padding: 10, boxWidth: 12 }
                }
            }
        }
    });

    // ── By-Category Bar Chart ─────────────────────────────────────────────────
    const byCategory = @json($byCategory);
    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: byCategory.map(d => d.category),
            datasets: [{
                label: 'Tasks',
                data: byCategory.map(d => d.count),
                backgroundColor: palette.map(c => c + 'bf'),
                borderColor: palette,
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: labelColor, font: { size: 11 } }, grid: { color: gridColor } },
                y: { beginAtZero: true, ticks: { color: labelColor, font: { size: 11 }, stepSize: 1 }, grid: { color: gridColor } }
            }
        }
    });

    // ── Feedback Ratings Bar Chart ────────────────────────────────────────────
    const feedbackRatings = @json($feedbackRatings);
    const ratingColors = ['#ef4444','#f97316','#f59e0b','#84cc16','#22c55e'];
    new Chart(document.getElementById('ratingsChart'), {
        type: 'bar',
        data: {
            labels: ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
            datasets: [{
                label: 'Responses',
                data: [1,2,3,4,5].map(r => {
                    const match = feedbackRatings.find(d => d.rating == r);
                    return match ? match.count : 0;
                }),
                backgroundColor: ratingColors.map(c => c + 'bf'),
                borderColor: ratingColors,
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: labelColor, font: { size: 11 } }, grid: { color: gridColor } },
                y: { beginAtZero: true, ticks: { color: labelColor, font: { size: 11 }, stepSize: 1 }, grid: { color: gridColor } }
            }
        }
    });
})();
</script>
@endpush
@endsection
