<?php

namespace App\Domain\Catalog\Enums;

enum BookStatusEnum: string
{
    case DRAFT     = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED  = 'archived';
}
