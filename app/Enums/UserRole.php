<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Editor = 'editor';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => __('Super Admin'),
            self::Admin => __('Administrator'),
            self::Editor => __('Editor'),
            self::Pending => __('Pending'),
        };
    }

    /**
     * @return array<int, UserRole>
     */
    public function manages(): array
    {
        return match ($this) {
            self::SuperAdmin => [self::Admin, self::Editor, self::Pending],
            self::Admin => [self::Editor, self::Pending],
            default => [],
        };
    }
}
