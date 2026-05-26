<?php

namespace App\Domain\Reading\Entities;

final readonly class BookVolume
{
    public function __construct(
        public ?int     $id = null,
        public int      $bookId,
        public int      $number,
        public ?string  $title = null,
    ) {}
}
