<?php

namespace App\Enums;

enum FeedExecutionStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';

    /**
     * Get all available status values
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
            self::PENDING => 'Menunggu',
            self::SUCCESS => 'Berhasil',
            self::FAILED => 'Gagal',
        };
    }

    /**
     * Get badge color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::SUCCESS => 'success',
            self::FAILED => 'danger',
        };
    }
}

