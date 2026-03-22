<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceTask;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('maintenance.view'), 403);
        $query = MaintenanceTask::with('reporter')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $tasks      = $query->paginate(15)->withQueryString();
        $statuses   = MaintenanceTask::statuses();
        $categories = MaintenanceTask::categories();
        $priorities = MaintenanceTask::priorities();

        return view('staff.maintenance.index', compact('tasks', 'statuses', 'categories', 'priorities'));
    }

    public function update(Request $request, MaintenanceTask $maintenanceTask)
    {
        abort_unless(auth()->user()->hasPermission('maintenance.manage'), 403);
        $data = $request->validate([
            'status' => 'required|in:Reported,Assessed,In Progress,Completed,Deferred',
        ]);

        $old = $maintenanceTask->status;
        $maintenanceTask->update($data + ['assigned_to' => auth()->id()]);

        ActivityLogger::log(
            'status_changed',
            "Maintenance task '{$maintenanceTask->title}' status changed from '{$old}' to '{$data['status']}'",
            'MaintenanceTask', $maintenanceTask->id,
            ['old_status' => $old, 'new_status' => $data['status']]
        );

        return back()->with('success', 'Task updated.');
    }
}
