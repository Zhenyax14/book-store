<?php

declare(strict_types=1);

namespace Tests\Traits;

use Exception;
use Illuminate\Support\Facades\Storage;

trait RequiresMinIO
{
    protected function ensureMinioIsAccessible(): void
    {
        try {
            Storage::disk('s3')->exists('health-check');
        } catch (Exception $e) {
            $this->markTestSkipped('MinIO is unavailable: ' . $e->getMessage());
        }
    }
}
