<?php

namespace App\Infrastructure\Slugger;

use App\Application\Shared\Interfaces\SlugGeneratorInterface;
use Illuminate\Support\Str;

class LaravelSlugGenerator implements SlugGeneratorInterface
{
    public function generate(string $source): string
    {
        return Str::slug($source);
    }
}
