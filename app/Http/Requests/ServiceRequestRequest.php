<?php

namespace App\Http\Requests;

use App\Rules\NotSpam;
use Illuminate\Foundation\Http\FormRequest;

class ServiceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'        => 'required|in:Clearance,Certificate,Indigency,Complaint,Maintenance,Other',
            'subject'     => ['required', 'string', 'min:5', 'max:255', new NotSpam],
            'description' => ['required', 'string', 'min:10', new NotSpam],
        ];
    }
}
