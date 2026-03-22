<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name'     => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'middle_name'    => ['nullable', 'string', 'max:100'],
            'surname'        => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'suffix'         => ['nullable', 'string', 'max:20'],
            'email'          => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password'       => ['required', 'confirmed', Rules\Password::min(6)],
            'contact_number' => ['nullable', 'regex:/^09\d{9}$/'],
            'birthdate'      => ['nullable', 'date', 'before:today'],
            'gender'         => ['nullable', 'string', 'in:Male,Female,Non-binary,Prefer not to say'],
            'civil_status'   => ['nullable', 'string', 'in:Single,Married,Widowed,Separated,Divorced'],
            'current_place'  => ['nullable', 'string', 'max:255'],
        ]);

        // Compose display name from first + surname (+ suffix if present)
        $name = trim($request->first_name . ' ' . $request->surname);
        if ($request->filled('suffix')) {
            $name .= ' ' . trim($request->suffix);
        }

        $user = DB::transaction(function () use ($request, $name) {
            $user = User::create([
                'name'           => $name,
                'email'          => $request->email,
                'password'       => Hash::make($request->password),
                'role'           => 'resident',
                'contact_number' => $request->contact_number,
            ]);

            Profile::create([
                'user_id'        => $user->id,
                'first_name'     => $request->first_name,
                'middle_name'    => $request->middle_name,
                'surname'        => $request->surname,
                'suffix'         => $request->suffix,
                'contact_number' => $request->contact_number,
                'birthdate'      => $request->birthdate,
                'gender'         => $request->gender,
                'civil_status'   => $request->civil_status,
                'current_place'  => $request->current_place,
            ]);

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('resident.dashboard');
    }
}
