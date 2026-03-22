<?php

namespace App\Observers;

use App\Models\BrgyNotification;
use App\Models\ServiceRequest;

class ServiceRequestObserver
{
    public function updated(ServiceRequest $serviceRequest): void
    {
        if ($serviceRequest->isDirty('status')) {
            BrgyNotification::create([
                'user_id'        => $serviceRequest->user_id,
                'title'          => 'Service Request Updated',
                'message'        => "Your request \"{$serviceRequest->subject}\" status changed to {$serviceRequest->status}.",
                'type'           => 'request_update',
                'reference_id'   => $serviceRequest->id,
                'reference_type' => 'service_request',
            ]);
        }
    }
}
