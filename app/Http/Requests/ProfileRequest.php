<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profileId = auth()->user()->profile?->id;

        return [
            'confirm_current_password' => 'required',
            'name'           => 'required|string|min:2|max:255',
            'first_name'     => 'nullable|string|min:2|max:100|regex:/^[\pL\s\-]+$/u',
            'middle_name'    => 'nullable|string|max:100',
            'surname'        => 'nullable|string|min:2|max:100|regex:/^[\pL\s\-]+$/u',
            'suffix'         => 'nullable|string|max:20',
            'contact_number' => [
                'nullable',
                'regex:/^09\d{9}$/',
                Rule::unique('profiles', 'contact_number')->ignore($profileId),
            ],
            'birthdate'      => 'nullable|date',
            'current_place'  => 'nullable|string|max:255',
            'gender'         => 'nullable|string|max:20',
            'civil_status'   => 'nullable|string|max:50',
            'avatar_url'     => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'confirm_current_password.required' => 'Please enter your current password to save changes.',
            'contact_number.regex'  => 'Contact number must be 11 digits and start with 09.',
            'contact_number.unique' => 'This contact number is already in use.',
        ];
    }
}
