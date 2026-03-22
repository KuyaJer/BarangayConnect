<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Human-friendly badge label for the action. */
    public function actionLabel(): string
    {
        return match ($this->action) {
            'login'            => 'Login',
            'logout'           => 'Logout',
            'created'          => 'Created',
            'updated'          => 'Updated',
            'deleted'          => 'Deleted',
            'status_changed'   => 'Status Changed',
            'password_changed' => 'Password Changed',
            default            => ucfirst($this->action),
        };
    }

    /** Tailwind colour classes per action. */
    public function actionColor(): string
    {
        return match ($this->action) {
            'login'            => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'logout'           => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
            'created'          => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
            'updated'          => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
            'deleted'          => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            'status_changed'   => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
            'password_changed' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
            default            => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        };
    }
}
