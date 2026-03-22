<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasUuids;

    protected $table = 'feedback';

    protected $fillable = [
        'request_id',
        'user_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
