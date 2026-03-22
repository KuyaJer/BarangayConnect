<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('complaints.view'), 403);
        $query = Complaint::with('user')->latest();

        if ($request->filled('search')) {
            $query->where('subject', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $complaints = $query->paginate(15)->withQueryString();
        $statuses   = Complaint::statuses();

        return view('staff.complaints.index', compact('complaints', 'statuses'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        abort_unless(auth()->user()->hasPermission('complaints.manage'), 403);
        $data = $request->validate([
            'status'     => 'required|in:Filed,Under Review,Resolved,Dismissed',
            'resolution' => 'nullable|string',
        ]);

        $old = $complaint->status;
        $complaint->update($data + ['assigned_to' => auth()->id()]);

        ActivityLogger::log(
            'status_changed',
            "Complaint '{$complaint->subject}' status changed from '{$old}' to '{$data['status']}'",
            'Complaint', $complaint->id,
            ['old_status' => $old, 'new_status' => $data['status']]
        );

        return back()->with('success', 'Complaint updated.');
    }
}
