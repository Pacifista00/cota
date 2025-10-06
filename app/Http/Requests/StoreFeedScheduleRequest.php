<?php

namespace App\Http\Requests;

use App\Enums\ScheduleFrequency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeedScheduleRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'waktu_pakan' => ['required', 'date_format:H:i:s'],
            'start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
            'frequency_type' => ['nullable', 'string', Rule::in(ScheduleFrequency::values())],
            'frequency_data' => ['nullable', 'array'],
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
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini.',
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

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation(): void
    {
        // Auto-set start_date to today if not provided
        if (!$this->has('start_date')) {
            $this->merge([
                'start_date' => now()->toDateString(),
            ]);
        }

        // Auto-set frequency_type to daily if not provided
        if (!$this->has('frequency_type')) {
            $this->merge([
                'frequency_type' => 'daily',
            ]);
        }

        // Add authenticated user ID if available
        if (auth()->check() && !$this->has('user_id')) {
            $this->merge([
                'user_id' => auth()->id(),
            ]);
        }
    }
}

