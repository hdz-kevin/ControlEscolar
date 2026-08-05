<?php

namespace App\Enums;

/**
 * UserRole enum represents the different roles a user can have in the system.
 */
enum UserRole: string
{
    case Admin = 'admin';
    case Teacher = 'teacher';
    case Student = 'student';

    /**
     * Get an array of all the enum values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }
}
