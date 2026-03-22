<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
use App\Http\Requests\ServiceRequestRequest;
use App\Models\Feedback;
use App\Models\ServiceRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('service_requests.view'), 403);
        $query = ServiceRequest::with('feedback')->where('user_id', auth()->id())->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15)->withQueryString();
        $statuses = ServiceRequest::statuses();
        $types    = ServiceRequest::types();

        return view('resident.service-requests.index', compact('requests', 'statuses', 'types'));
    }

    public function store(ServiceRequestRequest $request)
    {
        abort_unless(auth()->user()->hasPermission('service_requests.create'), 403);
        $sr = ServiceRequest::create($request->validated() + ['user_id' => auth()->id()]);

        ActivityLogger::log(
            'created',
            "Service request '{$sr->subject}' ({$sr->type}) submitted",
            'ServiceRequest', $sr->id
        );

        return back()->with('success', 'Request submitted successfully.');
    }

    public function update(ServiceRequestRequest $request, ServiceRequest $serviceRequest)
    {
        abort_unless(auth()->user()->hasPermission('service_requests.update'), 403);
        if ($serviceRequest->user_id !== auth()->id() || $serviceRequest->status !== 'Pending') {
            abort(403);
        }

        $serviceRequest->update($request->validated());

        ActivityLogger::log(
            'updated',
            "Service request '{$serviceRequest->subject}' edited",
            'ServiceRequest', $serviceRequest->id
        );

        return back()->with('success', 'Request updated.');
    }

    public function storeFeedback(FeedbackRequest $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->user_id !== auth()->id() || $serviceRequest->status !== 'Completed') {
            abort(403);
        }

        Feedback::updateOrCreate(
            ['request_id' => $serviceRequest->id, 'user_id' => auth()->id()],
            $request->validated()
        );

        ActivityLogger::log(
            'created',
            "Feedback submitted for service request '{$serviceRequest->subject}'",
            'ServiceRequest', $serviceRequest->id
        );

        return back()->with('success', 'Thank you for your feedback!');
    }
}
