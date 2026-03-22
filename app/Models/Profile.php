<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'surname',
        'suffix',
        'avatar_url',
        'contact_number',
        'birthdate',
        'current_place',
        'gender',
        'civil_status',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->surname,
            $this->suffix,
        ])));
    }
}
