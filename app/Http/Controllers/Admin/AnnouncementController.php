<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest;
use App\Models\Announcement;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::with('author')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $announcements = $query->paginate(15)->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(AnnouncementRequest $request)
    {
        $announcement = Announcement::create($request->validated() + ['created_by' => auth()->id()]);

        ActivityLogger::log(
            'created',
            "Announcement '{$announcement->title}' posted ({$announcement->priority})",
            'Announcement', $announcement->id
        );

        return back()->with('success', 'Announcement posted.');
    }

    public function update(AnnouncementRequest $request, Announcement $announcement)
    {
        $announcement->update($request->validated());

        ActivityLogger::log(
            'updated',
            "Announcement '{$announcement->title}' updated",
            'Announcement', $announcement->id
        );

        return back()->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $title = $announcement->title;
        $announcement->delete();

        ActivityLogger::log('deleted', "Announcement '{$title}' deleted", 'Announcement');

        return back()->with('success', 'Announcement deleted.');
    }
}
