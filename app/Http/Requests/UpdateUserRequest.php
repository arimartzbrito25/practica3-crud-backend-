<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * PUT = todos requeridos. PATCH = todos opcionales (sometimes).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $isPartial = $this->isMethod('patch');

        return [
            'name'     => $isPartial ? ['sometimes', 'string', 'max:255'] : ['required', 'string', 'max:255'],
            'lastname' => $isPartial ? ['sometimes', 'string', 'max:255'] : ['required', 'string', 'max:255'],
            'username' => $isPartial
                ? ['sometimes', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user)]
                : ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user)],
            'email'    => $isPartial
                ? ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user)]
                : ['required', 'email', Rule::unique('users', 'email')->ignore($user)],
        ];
    }
}
