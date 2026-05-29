<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\ValueObjects;

use App\Domain\Dashboard\Enums\StatCardTypeEnum;

final readonly class StatCard
{
    public function __construct(
        public StatCardTypeEnum $label,
        public string           $value,
        public string           $delta,
        public bool             $isUp,
    ) {}
}
