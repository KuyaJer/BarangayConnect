<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MaintenanceTask extends Model
{
    use HasUuids;

    protected $table = 'maintenance_tasks';

    protected $fillable = [
        'reported_by',
        'assigned_to',
        'title',
        'description',
        'category',
        'location',
        'priority',
        'status',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public static function categories(): array
    {
        return ['Infrastructure', 'Facility', 'Road', 'Drainage', 'Streetlight', 'Water System', 'Other'];
    }

    public static function priorities(): array
    {
        return ['Low', 'Normal', 'High', 'Urgent'];
    }

    public static function statuses(): array
    {
        return ['Reported', 'Assessed', 'In Progress', 'Completed', 'Deferred'];
    }
}
