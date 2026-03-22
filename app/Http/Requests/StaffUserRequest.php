<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'           => 'required|string|min:2|max:255',
            'email'          => 'required|email|unique:users,email,' . $userId,
            'password'       => $this->isMethod('POST') ? 'required|min:6' : 'nullable|min:6',
            'role'           => 'required|in:admin,staff,resident',
            'contact_number' => ['nullable', 'regex:/^(09|\+639)\d{9}$/'],
        ];
    }
}
