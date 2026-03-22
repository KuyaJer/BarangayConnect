<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user    = auth()->user();
        $profile = $user->profile ?? Profile::create(['user_id' => $user->id]);

        return view('shared.profile', compact('user', 'profile'));
    }

    public function update(ProfileRequest $request)
    {
        $user = auth()->user();

        // Verify current password before saving any profile changes
        if (!Hash::check($request->confirm_current_password, $user->password)) {
            return back()->withErrors(['confirm_current_password' => 'Incorrect password. Please try again.']);
        }

        $profile = $user->profile ?? Profile::create(['user_id' => $user->id]);

        $user->update(['name' => $request->name]);

        $profile->update($request->only([
            'first_name', 'middle_name', 'surname', 'suffix',
            'contact_number', 'birthdate', 'current_place', 'gender', 'civil_status', 'avatar_url',
        ]));

        ActivityLogger::log('updated', "Profile updated by '{$user->name}'", 'Profile', $user->id);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);

        ActivityLogger::log('password_changed', "Password changed by '" . auth()->user()->name . "'");

        return back()->with('success', 'Password changed successfully.');
    }
}
