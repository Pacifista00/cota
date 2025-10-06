<?php

namespace App\Http\Requests;

use App\Enums\ScheduleFrequency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFeedScheduleRequest extends FormRequest
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
        return [
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'waktu_pakan' => ['sometimes', 'required', 'date_format:H:i:s'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['sometimes', 'boolean'],
            'frequency_type' => ['sometimes', 'nullable', 'string', Rule::in(ScheduleFrequency::values())],
            'frequency_data' => ['sometimes', 'nullable', 'array'],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'waktu_pakan.required' => 'Waktu pakan wajib diisi.',
            'waktu_pakan.date_format' => 'Format waktu pakan harus HH:MM:SS (contoh: 08:00:00).',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'frequency_type.in' => 'Tipe frekuensi tidak valid.',
        ];
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama jadwal',
            'description' => 'deskripsi',
            'waktu_pakan' => 'waktu pakan',
            'start_date' => 'tanggal mulai',
            'end_date' => 'tanggal selesai',
            'is_active' => 'status aktif',
            'frequency_type' => 'tipe frekuensi',
            'frequency_data' => 'data frekuensi',
        ];
    }
}

