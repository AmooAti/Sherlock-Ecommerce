<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterCustomerRequest extends FormRequest
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
            'firstname'    => ['required', 'string', 'max:50'],
            'lastname'     => ['required', 'string', 'max:50'],
            'email'        => ['required', 'string', 'email', 'max:50', Rule::unique('customers')],
            'password'     => ['required', 'string', Password::min(8)->numbers()->mixedCase()],
            'phone_number' => ['nullable', 'string'],
            'last_login'   => ['nullable'],
        ];
    }
}
