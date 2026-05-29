<?php

declare(strict_types=1);

namespace App\Application\Notification\Interfaces;

interface NotificationJobInterface
{
    public function handle(): void;
}
