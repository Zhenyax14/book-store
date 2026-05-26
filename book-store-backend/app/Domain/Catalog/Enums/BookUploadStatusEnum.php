<?php

namespace App\Domain\Catalog\Enums;

enum BookUploadStatusEnum: string
{
    case PROCESSING = 'processing';
    case UPLOADED = 'uploaded';
    case FAILED  = 'failed';
}
