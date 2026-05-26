<?php

namespace App\Domain\Reading\Enums;

enum ContentFormatEnum: string
{
    case HTML = 'html';
    case MARKDOWN = 'markdown';
    case TEXT = 'text';
}
