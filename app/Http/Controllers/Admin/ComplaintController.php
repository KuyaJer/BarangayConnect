<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with('user', 'assignedTo')->latest();

        if ($request->filled('search')) {
            $query->where('subject', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $complaints = $query->paginate(15)->withQueryString();
        $statuses   = Complaint::statuses();

        return view('admin.complaints.index', compact('complaints', 'statuses'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $data = $request->validate([
            'status'      => 'required|in:Filed,Under Review,Resolved,Dismissed',
            'resolution'  => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $old = $complaint->status;
        $complaint->update($data);

        ActivityLogger::log(
            'status_changed',
            "Complaint '{$complaint->subject}' status changed from '{$old}' to '{$data['status']}'",
            'Complaint', $complaint->id,
            ['old_status' => $old, 'new_status' => $data['status']]
        );

        return back()->with('success', 'Complaint updated successfully.');
    }
}
