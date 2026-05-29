<?php

namespace App\Application\Shared\Interfaces;

interface SlugGeneratorInterface
{
    public function generate(string $source): string;
}
