<?php

namespace App\Http\Requests;

use App\Rules\NotSpam;
use Illuminate\Foundation\Http\FormRequest;

class MaintenanceTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'min:5', 'max:255', new NotSpam],
            'description' => ['required', 'string', 'min:10', new NotSpam],
            'category'    => 'required|in:Infrastructure,Facility,Road,Drainage,Streetlight,Water System,Other',
            'location'    => 'required|string|max:255',
            'priority'    => 'required|in:Low,Normal,High,Urgent',
        ];
    }
}
