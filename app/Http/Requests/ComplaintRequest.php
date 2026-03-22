<?php

namespace App\Http\Requests;

use App\Rules\NotSpam;
use Illuminate\Foundation\Http\FormRequest;

class ComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject'     => ['required', 'string', 'min:5', 'max:255', new NotSpam],
            'description' => ['required', 'string', 'min:10', new NotSpam],
        ];
    }
}
