<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Complaint;
use App\Models\ServiceRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalRequests     = ServiceRequest::where('user_id', $user->id)->count();
        $pendingRequests   = ServiceRequest::where('user_id', $user->id)->where('status', 'Pending')->count();
        $completedRequests = ServiceRequest::where('user_id', $user->id)->where('status', 'Completed')->count();
        $totalComplaints   = Complaint::where('user_id', $user->id)->count();

        $recentRequests = ServiceRequest::where('user_id', $user->id)->latest()->limit(5)->get();
        $announcements  = Announcement::latest()->limit(5)->get();

        return view('resident.dashboard', compact(
            'totalRequests', 'pendingRequests', 'completedRequests',
            'totalComplaints', 'recentRequests', 'announcements'
        ));
    }
}
