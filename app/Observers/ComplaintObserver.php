<?php

namespace App\Observers;

use App\Models\BrgyNotification;
use App\Models\Complaint;

class ComplaintObserver
{
    public function updated(Complaint $complaint): void
    {
        if ($complaint->isDirty('status')) {
            BrgyNotification::create([
                'user_id'        => $complaint->user_id,
                'title'          => 'Complaint Status Updated',
                'message'        => "Your complaint \"{$complaint->subject}\" status changed to {$complaint->status}.",
                'type'           => 'complaint_update',
                'reference_id'   => $complaint->id,
                'reference_type' => 'complaint',
            ]);
        }
    }
}
