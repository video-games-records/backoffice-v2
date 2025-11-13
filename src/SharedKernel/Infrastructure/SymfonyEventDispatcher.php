<?php

namespace App\SharedKernel\Infrastructure;

use App\SharedKernel\Domain\Event\DomainEvent;
use App\SharedKernel\Domain\Interface\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

final class SymfonyEventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private readonly SymfonyEventDispatcherInterface $eventDispatcher
    ) {
    }

    public function dispatch(DomainEvent $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }
}
