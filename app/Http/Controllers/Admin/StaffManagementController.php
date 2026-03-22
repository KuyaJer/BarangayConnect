<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffUserRequest;
use App\Models\Profile;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.staff.index', compact('users'));
    }

    public function store(StaffUserRequest $request)
    {
        $newUser = null;
        DB::transaction(function () use ($request, &$newUser) {
            $newUser = User::create([
                'name'           => $request->name,
                'email'          => $request->email,
                'password'       => Hash::make($request->password),
                'role'           => $request->role,
                'contact_number' => $request->contact_number,
            ]);

            Profile::create(['user_id' => $newUser->id]);
        });

        ActivityLogger::log(
            'created',
            "User '{$newUser->name}' ({$newUser->role}) account created",
            'User', $newUser->id
        );

        return back()->with('success', 'User created successfully.');
    }

    public function update(StaffUserRequest $request, User $user)
    {
        $data = [
            'name'           => $request->name,
            'email'          => $request->email,
            'role'           => $request->role,
            'contact_number' => $request->contact_number,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        ActivityLogger::log(
            'updated',
            "User '{$user->name}' ({$user->role}) account updated",
            'User', $user->id
        );

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $role = $user->role;
        $user->delete();

        ActivityLogger::log('deleted', "User '{$name}' ({$role}) account deleted", 'User');

        return back()->with('success', 'User deleted.');
    }
}
