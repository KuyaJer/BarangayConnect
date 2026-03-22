<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ── Permission definitions ──────────────────────────────────────
        $permissions = [
            // Service Requests
            ['name' => 'service_requests.view',   'display_name' => 'View Service Requests',   'group' => 'Service Requests', 'description' => 'View service requests list'],
            ['name' => 'service_requests.create',  'display_name' => 'Submit Service Requests',  'group' => 'Service Requests', 'description' => 'Create/submit new service requests'],
            ['name' => 'service_requests.update',  'display_name' => 'Edit Own Service Requests','group' => 'Service Requests', 'description' => 'Edit own pending service requests'],
            ['name' => 'service_requests.manage',  'display_name' => 'Manage Service Requests',  'group' => 'Service Requests', 'description' => 'Update status of any service request'],

            // Complaints
            ['name' => 'complaints.view',   'display_name' => 'View Complaints',   'group' => 'Complaints', 'description' => 'View complaints list'],
            ['name' => 'complaints.create',  'display_name' => 'File Complaints',   'group' => 'Complaints', 'description' => 'File/submit new complaints'],
            ['name' => 'complaints.update',  'display_name' => 'Edit Own Complaints','group' => 'Complaints', 'description' => 'Edit own pending complaints'],
            ['name' => 'complaints.manage',  'display_name' => 'Manage Complaints',  'group' => 'Complaints', 'description' => 'Update status of any complaint'],

            // Maintenance
            ['name' => 'maintenance.view',   'display_name' => 'View Maintenance Tasks',   'group' => 'Maintenance', 'description' => 'View maintenance tasks list'],
            ['name' => 'maintenance.create',  'display_name' => 'Report Maintenance Issues', 'group' => 'Maintenance', 'description' => 'Report new maintenance issues'],
            ['name' => 'maintenance.update',  'display_name' => 'Edit Own Reports',          'group' => 'Maintenance', 'description' => 'Edit own pending maintenance reports'],
            ['name' => 'maintenance.manage',  'display_name' => 'Manage Maintenance Tasks',  'group' => 'Maintenance', 'description' => 'Update status of any maintenance task'],

            // Residents
            ['name' => 'residents.view',   'display_name' => 'View Resident Directory', 'group' => 'Residents', 'description' => 'Browse the resident directory'],
            ['name' => 'residents.create',  'display_name' => 'Add Residents',           'group' => 'Residents', 'description' => 'Add new residents to the directory'],
            ['name' => 'residents.update',  'display_name' => 'Edit Residents',          'group' => 'Residents', 'description' => 'Update resident information'],
            ['name' => 'residents.delete',  'display_name' => 'Delete Residents',        'group' => 'Residents', 'description' => 'Remove residents from the directory'],

            // Announcements
            ['name' => 'announcements.view',   'display_name' => 'View Announcements',   'group' => 'Announcements', 'description' => 'View barangay announcements'],
            ['name' => 'announcements.create',  'display_name' => 'Post Announcements',   'group' => 'Announcements', 'description' => 'Create new announcements'],
            ['name' => 'announcements.update',  'display_name' => 'Edit Announcements',   'group' => 'Announcements', 'description' => 'Edit existing announcements'],
            ['name' => 'announcements.delete',  'display_name' => 'Delete Announcements', 'group' => 'Announcements', 'description' => 'Delete announcements'],

            // Staff Management (admin-only by default)
            ['name' => 'staff.view',   'display_name' => 'View Staff List',  'group' => 'Staff Management', 'description' => 'View staff members'],
            ['name' => 'staff.create',  'display_name' => 'Add Staff',        'group' => 'Staff Management', 'description' => 'Create new staff accounts'],
            ['name' => 'staff.update',  'display_name' => 'Edit Staff',       'group' => 'Staff Management', 'description' => 'Edit staff information'],
            ['name' => 'staff.delete',  'display_name' => 'Remove Staff',     'group' => 'Staff Management', 'description' => 'Delete staff accounts'],

            // Reports
            ['name' => 'reports.view', 'display_name' => 'View Reports', 'group' => 'Reports', 'description' => 'Access the reports section'],

            // Activity Logs (admin-only)
            ['name' => 'activity_logs.view', 'display_name' => 'View Activity Logs', 'group' => 'Security', 'description' => 'Access audit trail / activity logs'],

            // Profile (all roles)
            ['name' => 'profile.update',   'display_name' => 'Update Own Profile', 'group' => 'Profile', 'description' => 'Edit own profile information'],
            ['name' => 'profile.password', 'display_name' => 'Change Own Password','group' => 'Profile', 'description' => 'Change own account password'],
        ];

        // Insert permissions (skip if already exist)
        foreach ($permissions as $p) {
            DB::table('permissions')->insertOrIgnore([
                'id'           => Str::uuid(),
                'name'         => $p['name'],
                'display_name' => $p['display_name'],
                'group'        => $p['group'],
                'description'  => $p['description'] ?? null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        // ── Role permission assignments ─────────────────────────────────
        // Admin: hasPermission() always returns true — no rows needed
        // Staff: can view+manage service requests, complaints, maintenance; view residents+announcements+reports
        $staffPermissions = [
            'service_requests.view', 'service_requests.manage',
            'complaints.view',       'complaints.manage',
            'maintenance.view',      'maintenance.manage',
            'residents.view',
            'announcements.view',
            'reports.view',
            'profile.update', 'profile.password',
        ];

        // Resident: can view+create+update their own service requests, complaints, maintenance; view announcements
        $residentPermissions = [
            'service_requests.view', 'service_requests.create', 'service_requests.update',
            'complaints.view',       'complaints.create',       'complaints.update',
            'maintenance.view',      'maintenance.create',      'maintenance.update',
            'announcements.view',
            'profile.update', 'profile.password',
        ];

        $this->insertRolePermissions('staff',    $staffPermissions);
        $this->insertRolePermissions('resident', $residentPermissions);
    }

    private function insertRolePermissions(string $role, array $permissions): void
    {
        foreach ($permissions as $permission) {
            DB::table('role_permissions')->insertOrIgnore([
                'role'       => $role,
                'permission' => $permission,
            ]);
        }
    }
}
