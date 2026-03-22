<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $residentId = $this->route('resident')?->id;

        return [
            'first_name'         => 'required|string|min:2|max:100|regex:/^[\pL\s\-]+$/u',
            'last_name'          => 'required|string|min:2|max:100|regex:/^[\pL\s\-]+$/u',
            'address'            => 'required|string',
            'contact_number'     => [
                'nullable',
                'regex:/^09\d{9}$/',
                Rule::unique('residents', 'contact_number')->ignore($residentId),
            ],
            'email'              => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('residents', 'email')->ignore($residentId),
            ],
            'household_id'       => 'nullable|string|max:50',
            'purok'              => 'nullable|string|max:100',
            'resident_id_number' => 'nullable|integer',
            'verified'           => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'contact_number.regex'  => 'Contact number must be 11 digits and start with 09.',
            'contact_number.unique' => 'This contact number is already registered to another resident.',
            'email.unique'          => 'This email is already registered to another resident.',
        ];
    }
}
