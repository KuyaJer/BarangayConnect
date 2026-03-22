<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Http\Requests\ComplaintRequest;
use App\Models\Complaint;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('complaints.view'), 403);
        $query = Complaint::where('user_id', auth()->id())->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $complaints = $query->paginate(15)->withQueryString();
        $statuses   = Complaint::statuses();

        return view('resident.complaints.index', compact('complaints', 'statuses'));
    }

    public function store(ComplaintRequest $request)
    {
        abort_unless(auth()->user()->hasPermission('complaints.create'), 403);
        $complaint = Complaint::create($request->validated() + ['user_id' => auth()->id()]);

        ActivityLogger::log(
            'created',
            "Complaint '{$complaint->subject}' filed",
            'Complaint', $complaint->id
        );

        return back()->with('success', 'Complaint filed successfully.');
    }

    public function update(ComplaintRequest $request, Complaint $complaint)
    {
        abort_unless(auth()->user()->hasPermission('complaints.update'), 403);
        if ($complaint->user_id !== auth()->id() || $complaint->status !== 'Filed') {
            abort(403);
        }

        $complaint->update($request->validated());

        ActivityLogger::log(
            'updated',
            "Complaint '{$complaint->subject}' edited",
            'Complaint', $complaint->id
        );

        return back()->with('success', 'Complaint updated.');
    }
}
