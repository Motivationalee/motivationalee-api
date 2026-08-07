<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EnquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'organisation' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'event_date' => ['required', 'date'],
            'venue' => ['required', 'string', 'max:255'],
            'audience_size' => ['required', 'integer', 'min:0'],
            'event_type' => ['required', 'string', 'max:255'],
            'service' => ['required', 'string', 'max:255'],
            'budget' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'status' => ['nullable', 'string', 'in:pending,approved,rejected']
        ];
    }
}
