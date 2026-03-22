<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'address',
        'contact_number',
        'email',
        'household_id',
        'purok',
        'resident_id_number',
        'verified',
    ];

    protected $casts = [
        'verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }
}
