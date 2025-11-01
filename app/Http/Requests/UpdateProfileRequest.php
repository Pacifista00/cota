<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'fullname' => ['nullable', 'string', 'max:54'],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'no_telepon' => ['nullable', 'string', Rule::unique('users', 'no_telepon')->ignore($userId)],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'fullname.string' => 'Nama lengkap harus berupa teks.',
            'fullname.max' => 'Nama lengkap maksimal 54 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'no_telepon.string' => 'Nomor telepon harus berupa teks.',
            'no_telepon.unique' => 'Nomor telepon sudah digunakan.',
        ];
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'fullname' => 'nama lengkap',
            'email' => 'email',
            'no_telepon' => 'nomor telepon',
        ];
    }
}

