<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Complaint;
use App\Models\MaintenanceTask;
use App\Models\Profile;
use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Permissions ────────────────────────────────────────────────
        $this->call(PermissionSeeder::class);

        // ── Admin ──────────────────────────────────────────────────────
        $admin = User::create([
            'id'       => Str::uuid(),
            'name'     => 'Admin User',
            'email'    => 'admin@barangay.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);
        Profile::create(['id' => Str::uuid(), 'user_id' => $admin->id, 'first_name' => 'Admin', 'surname' => 'User']);

        // ── Staff ──────────────────────────────────────────────────────
        $staffData = [
            ['name' => 'Maria Santos', 'email' => 'maria@barangay.com'],
            ['name' => 'Jose Reyes',   'email' => 'jose@barangay.com'],
        ];
        $staffUsers = [];
        foreach ($staffData as $s) {
            $u = User::create([
                'id' => Str::uuid(), 'name' => $s['name'], 'email' => $s['email'],
                'password' => Hash::make('password'), 'role' => 'staff',
            ]);
            Profile::create(['id' => Str::uuid(), 'user_id' => $u->id]);
            $staffUsers[] = $u;
        }

        // ── Residents ──────────────────────────────────────────────────
        $residentNames = [
            ['name' => 'Juan dela Cruz', 'email' => 'juan@email.com'],
            ['name' => 'Ana Gonzales',   'email' => 'ana@email.com'],
            ['name' => 'Pedro Lim',      'email' => 'pedro@email.com'],
            ['name' => 'Rosa Aquino',    'email' => 'rosa@email.com'],
            ['name' => 'Carlos Tan',     'email' => 'carlos@email.com'],
            ['name' => 'Luz Mercado',    'email' => 'luz@email.com'],
            ['name' => 'Rico Navarro',   'email' => 'rico@email.com'],
            ['name' => 'Elena Castillo', 'email' => 'elena@email.com'],
            ['name' => 'Ben Torres',     'email' => 'ben@email.com'],
            ['name' => 'Cora Villanueva','email' => 'cora@email.com'],
        ];

        $residentUsers = [];
        foreach ($residentNames as $r) {
            $u = User::create([
                'id' => Str::uuid(), 'name' => $r['name'], 'email' => $r['email'],
                'password' => Hash::make('password'), 'role' => 'resident',
            ]);
            $parts = explode(' ', $r['name']);
            Profile::create([
                'id' => Str::uuid(), 'user_id' => $u->id,
                'first_name' => $parts[0], 'surname' => end($parts),
            ]);
            $residentUsers[] = $u;
        }

        // ── Resident Directory Records ─────────────────────────────────
        $puroks = ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4'];
        foreach ($residentUsers as $i => $u) {
            $parts = explode(' ', $u->name);
            Resident::create([
                'id'                 => Str::uuid(),
                'user_id'            => $u->id,
                'first_name'         => $parts[0],
                'last_name'          => end($parts),
                'address'            => ($i + 1) . ' Sampaguita Street, Barangay',
                'purok'              => $puroks[$i % 4],
                'contact_number'     => '09' . str_pad($i + 1, 9, '0', STR_PAD_LEFT),
                'email'              => $u->email,
                'resident_id_number' => 1000 + $i + 1,
                'verified'           => $i % 3 !== 0,
            ]);
        }

        // ── Announcements ──────────────────────────────────────────────
        $announcements = [
            ['title' => 'Barangay Assembly',             'content' => 'Monthly assembly on Saturday 9AM at the covered court.', 'priority' => 'Normal'],
            ['title' => 'Water Interruption Notice',      'content' => 'Water supply will be cut from 8AM to 5PM on Thursday.',   'priority' => 'Urgent'],
            ['title' => 'Free Medical Mission',           'content' => 'Free check-up and medicines on Friday at the health center.', 'priority' => 'Normal'],
            ['title' => 'Street Cleaning Schedule',       'content' => 'Community clean-up every Saturday 6AM-8AM.',              'priority' => 'Normal'],
            ['title' => 'Emergency Hotline Reminder',     'content' => 'Call 09XX-XXX-XXXX for barangay emergencies 24/7.',       'priority' => 'Urgent'],
        ];
        foreach ($announcements as $a) {
            Announcement::create(['id' => Str::uuid(), 'created_by' => $admin->id] + $a);
        }

        // ── Service Requests ───────────────────────────────────────────
        $types    = ['Clearance', 'Certificate', 'Indigency', 'Complaint', 'Maintenance', 'Other'];
        $statuses = ['Pending', 'Approved', 'In Progress', 'Completed', 'Rejected'];
        for ($i = 0; $i < 20; $i++) {
            ServiceRequest::create([
                'id'          => Str::uuid(),
                'user_id'     => $residentUsers[$i % count($residentUsers)]->id,
                'type'        => $types[$i % count($types)],
                'subject'     => 'Request #' . ($i + 1) . ' – ' . $types[$i % count($types)],
                'description' => 'This is a sample service request description for testing purposes.',
                'status'      => $statuses[$i % count($statuses)],
                'assigned_to' => $i % 3 === 0 ? $staffUsers[0]->id : null,
            ]);
        }

        // ── Complaints ─────────────────────────────────────────────────
        $cStatuses = ['Filed', 'Under Review', 'Resolved', 'Dismissed'];
        for ($i = 0; $i < 10; $i++) {
            Complaint::create([
                'id'          => Str::uuid(),
                'user_id'     => $residentUsers[$i % count($residentUsers)]->id,
                'subject'     => 'Complaint #' . ($i + 1),
                'description' => 'Sample complaint description. Requesting immediate attention.',
                'status'      => $cStatuses[$i % count($cStatuses)],
                'resolution'  => $i % 4 === 2 ? 'Issue has been resolved by the barangay office.' : null,
            ]);
        }

        // ── Maintenance Tasks ──────────────────────────────────────────
        $categories = ['Infrastructure', 'Facility', 'Road', 'Drainage', 'Streetlight', 'Water System', 'Other'];
        $mStatuses  = ['Reported', 'Assessed', 'In Progress', 'Completed', 'Deferred'];
        $mPriorities = ['Low', 'Normal', 'High', 'Urgent'];
        for ($i = 0; $i < 10; $i++) {
            MaintenanceTask::create([
                'id'          => Str::uuid(),
                'reported_by' => $residentUsers[$i % count($residentUsers)]->id,
                'title'       => 'Maintenance Issue #' . ($i + 1),
                'description' => 'Detailed description of the maintenance issue.',
                'category'    => $categories[$i % count($categories)],
                'location'    => 'Near landmark ' . ($i + 1),
                'priority'    => $mPriorities[$i % count($mPriorities)],
                'status'      => $mStatuses[$i % count($mStatuses)],
            ]);
        }
    }
}
