<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs    = $query->paginate(25)->withQueryString();
        $actions = ['login', 'logout', 'created', 'updated', 'deleted', 'status_changed', 'password_changed'];
        $users   = User::orderBy('name')->get(['id', 'name', 'role']);
        $subjects = ActivityLog::whereNotNull('subject_type')->distinct()->pluck('subject_type');

        return view('admin.activity-logs.index', compact('logs', 'actions', 'users', 'subjects'));
    }
}
