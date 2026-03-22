@props(['status'])

@php
$colors = [
    'Pending'      => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
    'Approved'     => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    'In Progress'  => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
    'Completed'    => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    'Rejected'     => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
    'Cancelled'    => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'Filed'        => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    'Under Review' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
    'Resolved'     => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    'Dismissed'    => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
    'Reported'     => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    'Assessed'     => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
    'Deferred'     => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'Normal'       => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'Urgent'       => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
    'Low'          => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    'High'         => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
    'admin'        => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
    'staff'        => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    'resident'     => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
];

$class = $colors[$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class }}">
    {{ $status }}
</span>
