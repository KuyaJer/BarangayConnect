<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceRequest::with('user', 'assignedTo')->latest();

        if ($request->filled('search')) {
            $query->where('subject', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15)->withQueryString();
        $statuses = ServiceRequest::statuses();

        return view('admin.service-requests.index', compact('requests', 'statuses'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'status'      => 'required|in:Pending,Approved,In Progress,Completed,Rejected,Cancelled',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $old = $serviceRequest->status;
        $serviceRequest->update($data);

        ActivityLogger::log(
            'status_changed',
            "Service request '{$serviceRequest->subject}' status changed from '{$old}' to '{$data['status']}'",
            'ServiceRequest', $serviceRequest->id,
            ['old_status' => $old, 'new_status' => $data['status']]
        );

        return back()->with('success', 'Request updated successfully.');
    }
}
