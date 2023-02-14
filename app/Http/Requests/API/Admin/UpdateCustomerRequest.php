<?php

namespace App\Http\Requests\API\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'firstname'    => ['nullable', 'string', 'max:255'],
            'lastname'     => ['nullable', 'string', 'max:255'],
            'email'        => ['nullable', 'string', 'email', 'max:255', Rule::unique('customers')],
            'password'     => ['nullable', 'string', Password::min(8)->numbers()->mixedCase()],
            'phone_number' => ['nullable', 'string'],
            'is_suspended' => ['nullable', 'string'],
        ];
    }
}