<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasUuids;

    protected $fillable = [
        'created_by',
        'title',
        'content',
        'priority',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function priorities(): array
    {
        return ['Normal', 'Urgent'];
    }
}
