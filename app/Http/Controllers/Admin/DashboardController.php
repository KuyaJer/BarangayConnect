<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Resident;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRequests    = ServiceRequest::count();
        $pendingRequests  = ServiceRequest::where('status', 'Pending')->count();
        $completedRequests = ServiceRequest::where('status', 'Completed')->count();
        $totalResidents   = Resident::count();

        $monthlyData = ServiceRequest::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        $byType = ServiceRequest::select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get();

        $recentRequests = ServiceRequest::with('user')
            ->latest()
            ->limit(5)
            ->get();

        $announcements = Announcement::with('author')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRequests', 'pendingRequests', 'completedRequests',
            'totalResidents', 'monthlyData', 'byType', 'recentRequests', 'announcements'
        ));
    }
}
