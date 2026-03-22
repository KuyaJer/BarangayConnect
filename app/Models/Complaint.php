<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'assigned_to',
        'subject',
        'description',
        'resolution',
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

    public static function statuses(): array
    {
        return ['Filed', 'Under Review', 'Resolved', 'Dismissed'];
    }
}
