<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Feedback;
use App\Models\MaintenanceTask;
use App\Models\Resident;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $totalRequests    = ServiceRequest::count();
        $totalComplaints  = Complaint::count();
        $totalResidents   = Resident::count();
        $totalMaintenance = MaintenanceTask::count();

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
            ->groupBy('type')->get();

        $byCategory = MaintenanceTask::select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')->get();

        $feedbackRatings = Feedback::select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')->orderBy('rating')->get();

        return view('admin.reports.index', compact(
            'totalRequests', 'totalComplaints', 'totalResidents', 'totalMaintenance',
            'monthlyData', 'byType', 'byCategory', 'feedbackRatings'
        ));
    }
}
