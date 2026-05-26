<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

enum RoleEnum: string
{
    case READER = 'reader';
    case ADMIN  = 'admin';
}
