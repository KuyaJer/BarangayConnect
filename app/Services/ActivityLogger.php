<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogger
{
    /**
     * Record an activity log entry.
     *
     * @param string      $action      login|logout|created|updated|deleted|status_changed|password_changed
     * @param string      $description Human-readable sentence
     * @param string|null $subjectType Model class short name, e.g. 'ServiceRequest'
     * @param string|null $subjectId   UUID of the affected record
     * @param array       $properties  Optional extra context (old/new values, etc.)
     */
    public static function log(
        string  $action,
        string  $description,
        ?string $subjectType = null,
        ?string $subjectId   = null,
        array   $properties  = []
    ): void {
        try {
            ActivityLog::create([
                'user_id'      => auth()->id(),
                'action'       => $action,
                'subject_type' => $subjectType,
                'subject_id'   => $subjectId,
                'description'  => $description,
                'properties'   => $properties ?: null,
                'ip_address'   => request()->ip(),
            ]);
        } catch (\Throwable) {
            // Never let logging break the main flow
        }
    }
}
