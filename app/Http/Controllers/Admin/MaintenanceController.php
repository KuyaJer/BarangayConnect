<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceTask;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceTask::with('reporter')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks      = $query->paginate(15)->withQueryString();
        $statuses   = MaintenanceTask::statuses();
        $categories = MaintenanceTask::categories();
        $priorities = MaintenanceTask::priorities();

        return view('admin.maintenance.index', compact('tasks', 'statuses', 'categories', 'priorities'));
    }

    public function update(Request $request, MaintenanceTask $maintenanceTask)
    {
        $data = $request->validate([
            'status'      => 'required|in:Reported,Assessed,In Progress,Completed,Deferred',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $old = $maintenanceTask->status;
        $maintenanceTask->update($data);

        ActivityLogger::log(
            'status_changed',
            "Maintenance task '{$maintenanceTask->title}' status changed from '{$old}' to '{$data['status']}'",
            'MaintenanceTask', $maintenanceTask->id,
            ['old_status' => $old, 'new_status' => $data['status']]
        );

        return back()->with('success', 'Task updated.');
    }
}
