<?php

declare(strict_types=1);

namespace App\Domain\Reading\Enums;

enum FontFamilyEnum: string
{
    case LORA = 'Lora';
    case PLAYFAIR_DISPLAY = 'Playfair Display';
    case GEORGIA = 'Georgia';
}
