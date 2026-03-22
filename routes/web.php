<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Resident;
use App\Http\Controllers\Staff;
use Illuminate\Support\Facades\Route;

// Root — redirect based on role
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'staff'  => redirect()->route('staff.dashboard'),
            default  => redirect()->route('resident.dashboard'),
        };
    }
    return redirect()->route('login');
});

// ────────────────────────────────────────────────
// ADMIN ROUTES
// ────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Service Requests
    Route::get('/service-requests', [Admin\ServiceRequestController::class, 'index'])->name('service-requests.index');
    Route::patch('/service-requests/{serviceRequest}', [Admin\ServiceRequestController::class, 'update'])->name('service-requests.update');

    // Complaints
    Route::get('/complaints', [Admin\ComplaintController::class, 'index'])->name('complaints.index');
    Route::patch('/complaints/{complaint}', [Admin\ComplaintController::class, 'update'])->name('complaints.update');

    // Residents
    Route::get('/residents', [Admin\ResidentController::class, 'index'])->name('residents.index');
    Route::post('/residents', [Admin\ResidentController::class, 'store'])->name('residents.store');
    Route::patch('/residents/{resident}', [Admin\ResidentController::class, 'update'])->name('residents.update');
    Route::delete('/residents/{resident}', [Admin\ResidentController::class, 'destroy'])->name('residents.destroy');

    // Announcements
    Route::get('/announcements', [Admin\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [Admin\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::patch('/announcements/{announcement}', [Admin\AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [Admin\AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // Maintenance
    Route::get('/maintenance', [Admin\MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::patch('/maintenance/{maintenanceTask}', [Admin\MaintenanceController::class, 'update'])->name('maintenance.update');

    // Reports
    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');

    // Activity Logs
    Route::get('/activity-logs', [Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Security: RBAC
    Route::get('/security/rbac', [Admin\RolePermissionController::class, 'index'])->name('security.rbac');
    Route::put('/security/rbac', [Admin\RolePermissionController::class, 'update'])->name('security.rbac.update');

    // Security: Backup & Recovery
    Route::get('/security/backup', [Admin\BackupController::class, 'index'])->name('security.backup');
    Route::post('/security/backup', [Admin\BackupController::class, 'create'])->name('security.backup.create');
    Route::get('/security/backup/{filename}/download', [Admin\BackupController::class, 'download'])->name('security.backup.download');
    Route::post('/security/backup/restore', [Admin\BackupController::class, 'restore'])->name('security.backup.restore');
    Route::delete('/security/backup/{filename}', [Admin\BackupController::class, 'destroy'])->name('security.backup.destroy');

    // Staff Management
    Route::get('/staff', [Admin\StaffManagementController::class, 'index'])->name('staff.index');
    Route::post('/staff', [Admin\StaffManagementController::class, 'store'])->name('staff.store');
    Route::patch('/staff/{user}', [Admin\StaffManagementController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{user}', [Admin\StaffManagementController::class, 'destroy'])->name('staff.destroy');
});

// ────────────────────────────────────────────────
// STAFF ROUTES
// ────────────────────────────────────────────────
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [Staff\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/service-requests', [Staff\ServiceRequestController::class, 'index'])->name('service-requests.index');
    Route::patch('/service-requests/{serviceRequest}', [Staff\ServiceRequestController::class, 'update'])->name('service-requests.update');

    Route::get('/complaints', [Staff\ComplaintController::class, 'index'])->name('complaints.index');
    Route::patch('/complaints/{complaint}', [Staff\ComplaintController::class, 'update'])->name('complaints.update');

    Route::get('/maintenance', [Staff\MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::patch('/maintenance/{maintenanceTask}', [Staff\MaintenanceController::class, 'update'])->name('maintenance.update');
});

// ────────────────────────────────────────────────
// RESIDENT ROUTES
// ────────────────────────────────────────────────
Route::middleware(['auth', 'role:resident'])->prefix('dashboard')->name('resident.')->group(function () {
    Route::get('/', [Resident\DashboardController::class, 'index'])->name('dashboard');

    // Service Requests
    Route::get('/service-requests', [Resident\ServiceRequestController::class, 'index'])->name('service-requests.index');
    Route::post('/service-requests', [Resident\ServiceRequestController::class, 'store'])->name('service-requests.store');
    Route::patch('/service-requests/{serviceRequest}', [Resident\ServiceRequestController::class, 'update'])->name('service-requests.update');
    Route::post('/service-requests/{serviceRequest}/feedback', [Resident\ServiceRequestController::class, 'storeFeedback'])->name('service-requests.feedback');

    // Complaints
    Route::get('/complaints', [Resident\ComplaintController::class, 'index'])->name('complaints.index');
    Route::post('/complaints', [Resident\ComplaintController::class, 'store'])->name('complaints.store');
    Route::patch('/complaints/{complaint}', [Resident\ComplaintController::class, 'update'])->name('complaints.update');

    // Maintenance
    Route::get('/maintenance', [Resident\MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::post('/maintenance', [Resident\MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::patch('/maintenance/{maintenanceTask}', [Resident\MaintenanceController::class, 'update'])->name('maintenance.update');

    // Announcements (view only)
    Route::get('/announcements', function () {
        $announcements = \App\Models\Announcement::with('author')
            ->when(request('search'), fn($q) => $q->where('title', 'like', '%' . request('search') . '%'))
            ->latest()->paginate(15)->withQueryString();
        return view('resident.announcements', compact('announcements'));
    })->name('announcements');
});

// ────────────────────────────────────────────────
// SHARED AUTHENTICATED ROUTES
// ────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/view', [NotificationController::class, 'view'])->name('notifications.view');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});

require __DIR__.'/auth.php';
