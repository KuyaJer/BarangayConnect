<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'contact_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isResident(): bool
    {
        return $this->role === 'resident';
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function residentRecord()
    {
        return $this->hasOne(Resident::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function maintenanceTasks()
    {
        return $this->hasMany(MaintenanceTask::class, 'reported_by');
    }

    public function brgyNotifications()
    {
        return $this->hasMany(BrgyNotification::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    /**
     * Check if the user's role has the given permission.
     * Admins always return true. Results are cached per request.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        static $cache = [];
        $key = $this->role . '|' . $permission;

        if (!array_key_exists($key, $cache)) {
            $cache[$key] = \Illuminate\Support\Facades\DB::table('role_permissions')
                ->where('role', $this->role)
                ->where('permission', $permission)
                ->exists();
        }

        return $cache[$key];
    }

    public function getAvatarInitialsAttribute(): string
    {
        $parts = explode(' ', $this->name);
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }
}
