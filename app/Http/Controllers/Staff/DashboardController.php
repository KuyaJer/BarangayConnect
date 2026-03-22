<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Resident;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRequests     = ServiceRequest::count();
        $pendingRequests   = ServiceRequest::where('status', 'Pending')->count();
        $completedRequests = ServiceRequest::where('status', 'Completed')->count();
        $totalResidents    = Resident::count();

        $recentRequests = ServiceRequest::with('user')->latest()->limit(5)->get();
        $announcements  = Announcement::with('author')->latest()->limit(5)->get();

        return view('staff.dashboard', compact(
            'totalRequests', 'pendingRequests', 'completedRequests',
            'totalResidents', 'recentRequests', 'announcements'
        ));
    }
}
