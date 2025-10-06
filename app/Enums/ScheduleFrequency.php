<?php

namespace App\Enums;

enum ScheduleFrequency: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case CUSTOM = 'custom';

    /**
     * Get all available frequency types
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match($this) {
            self::DAILY => 'Setiap Hari',
            self::WEEKLY => 'Setiap Minggu',
            self::MONTHLY => 'Setiap Bulan',
            self::CUSTOM => 'Kustom',
        };
    }

    /**
     * Get description
     */
    public function description(): string
    {
        return match($this) {
            self::DAILY => 'Pakan akan diberikan setiap hari pada waktu yang ditentukan',
            self::WEEKLY => 'Pakan akan diberikan setiap minggu pada hari dan waktu yang ditentukan',
            self::MONTHLY => 'Pakan akan diberikan setiap bulan pada tanggal dan waktu yang ditentukan',
            self::CUSTOM => 'Pakan akan diberikan sesuai pola kustom yang ditentukan',
        };
    }
}

