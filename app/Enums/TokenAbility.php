<?php

declare(strict_types=1);

namespace App\Enums;

enum TokenAbility: string
{
    case AccessApi = 'api:access';
    case ManageProfile = 'profile:manage';

    public const TOKEN_NAME = 'api-token';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $ability): string => $ability->value, self::cases());
    }
}
