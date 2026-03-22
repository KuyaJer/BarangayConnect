<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BrgyNotification extends Model
{
    use HasUuids;

    protected $table = 'brgy_notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'read',
        'type',
        'reference_id',
        'reference_type',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
