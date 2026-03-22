<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\BrgyNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = BrgyNotification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        $announcements = Announcement::latest()->limit(10)->get();

        return view('shared.notifications', compact('notifications', 'announcements'));
    }

    public function view(BrgyNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['read' => true]);

        $role  = auth()->user()->role;
        $route = match ($notification->reference_type) {
            'service_request' => match ($role) {
                'admin' => route('admin.service-requests.index'),
                'staff' => route('staff.service-requests.index'),
                default => route('resident.service-requests.index'),
            },
            'complaint' => match ($role) {
                'admin' => route('admin.complaints.index'),
                'staff' => route('staff.complaints.index'),
                default => route('resident.complaints.index'),
            },
            'maintenance' => match ($role) {
                'admin' => route('admin.maintenance.index'),
                'staff' => route('staff.maintenance.index'),
                default => route('resident.maintenance.index'),
            },
            default => route('notifications.index'),
        };

        return redirect($route);
    }

    public function markRead(BrgyNotification $notification)
    {
        if ($notification->user_id === auth()->id()) {
            $notification->update(['read' => true]);
        }

        return back();
    }

    public function markAllRead()
    {
        BrgyNotification::where('user_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
