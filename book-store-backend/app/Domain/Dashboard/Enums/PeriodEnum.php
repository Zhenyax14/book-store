<?php

namespace App\Domain\Dashboard\Enums;

enum PeriodEnum: string
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';
}
