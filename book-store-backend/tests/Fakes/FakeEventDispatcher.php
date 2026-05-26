<?php

namespace Tests\Fakes;

use App\Application\Catalog\Events\BookFileUploaded;
use App\Application\Shared\Interfaces\EventDispatcherInterface;
use App\Domain\Catalog\Events\BookPublished;
use App\Domain\Identity\Events\UserRegistered;
use App\Domain\Order\Events\PurchaseCompleted;
use App\Domain\Reading\Events\BookReadingFinished;
use PHPUnit\Framework\Assert;

class FakeEventDispatcher implements EventDispatcherInterface
{
    public function dispatch(object $event): void
    {
        $check = match (true) {
            $event instanceof BookFileUploaded,
            $event instanceof UserRegistered,
            $event instanceof BookReadingFinished,
            $event instanceof BookPublished,
            $event instanceof PurchaseCompleted => true,
            default => false,
        };
        Assert::assertTrue($check);
    }
}
