<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255|in:feedback,billing,technical,other',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
            'ip_address' => 'required|ip',
        ];
    }
}
