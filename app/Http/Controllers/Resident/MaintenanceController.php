<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaintenanceTaskRequest;
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

        return view('resident.maintenance.index', compact('tasks', 'statuses', 'categories', 'priorities'));
    }

    public function store(MaintenanceTaskRequest $request)
    {
        abort_unless(auth()->user()->hasPermission('maintenance.create'), 403);
        $task = MaintenanceTask::create($request->validated() + ['reported_by' => auth()->id()]);

        ActivityLogger::log(
            'created',
            "Maintenance issue '{$task->title}' ({$task->category}) reported",
            'MaintenanceTask', $task->id
        );

        return back()->with('success', 'Maintenance report submitted.');
    }

    public function update(MaintenanceTaskRequest $request, MaintenanceTask $maintenanceTask)
    {
        abort_unless(auth()->user()->hasPermission('maintenance.update'), 403);
        if ($maintenanceTask->reported_by !== auth()->id() || $maintenanceTask->status !== 'Reported') {
            abort(403);
        }

        $maintenanceTask->update($request->validated());

        ActivityLogger::log(
            'updated',
            "Maintenance report '{$maintenanceTask->title}' edited",
            'MaintenanceTask', $maintenanceTask->id
        );

        return back()->with('success', 'Report updated.');
    }
}
