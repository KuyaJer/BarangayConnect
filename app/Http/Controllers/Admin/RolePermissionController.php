<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    private const ROLES = ['staff', 'resident'];

    public function index()
    {
        $permissions = Permission::orderBy('group')->orderBy('display_name')->get();

        // Build a lookup: role => [permission_name => true]
        $assigned = [];
        foreach (self::ROLES as $role) {
            $assigned[$role] = DB::table('role_permissions')
                ->where('role', $role)
                ->pluck('permission')
                ->flip()
                ->map(fn() => true)
                ->toArray();
        }

        $groups = $permissions->groupBy('group');

        return view('admin.security.rbac', compact('groups', 'assigned'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'permissions'        => ['nullable', 'array'],
            'permissions.*.*'    => ['string'],
        ]);

        $allPermissions = Permission::pluck('name')->toArray();
        $submitted      = $validated['permissions'] ?? [];   // ['staff' => ['perm' => '1', ...], ...]

        foreach (self::ROLES as $role) {
            $before  = DB::table('role_permissions')->where('role', $role)->pluck('permission')->toArray();
            $newSet  = array_keys($submitted[$role] ?? []);
            $newSet  = array_values(array_intersect($newSet, $allPermissions)); // sanitize

            $toAdd    = array_diff($newSet, $before);
            $toRemove = array_diff($before, $newSet);

            if ($toAdd) {
                DB::table('role_permissions')->insert(
                    array_map(fn($p) => ['role' => $role, 'permission' => $p], $toAdd)
                );
            }
            if ($toRemove) {
                DB::table('role_permissions')
                    ->where('role', $role)
                    ->whereIn('permission', $toRemove)
                    ->delete();
            }
        }

        ActivityLogger::log(
            'updated',
            'Updated role permissions for: ' . implode(', ', self::ROLES),
            'RolePermissions'
        );

        return back()->with('success', 'Permissions updated successfully.');
    }
}
