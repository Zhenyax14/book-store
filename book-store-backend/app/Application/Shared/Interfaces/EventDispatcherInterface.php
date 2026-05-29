<?php

namespace App\Application\Shared\Interfaces;

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
}
