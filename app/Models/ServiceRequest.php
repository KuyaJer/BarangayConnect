<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasUuids;

    protected $table = 'service_requests';

    protected $fillable = [
        'user_id',
        'assigned_to',
        'type',
        'subject',
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'request_id');
    }

    public static function types(): array
    {
        return ['Clearance', 'Certificate', 'Indigency', 'Complaint', 'Maintenance', 'Other'];
    }

    public static function statuses(): array
    {
        return ['Pending', 'Approved', 'In Progress', 'Completed', 'Rejected', 'Cancelled'];
    }
}
