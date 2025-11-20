<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Badge\Infrastructure\Doctrine\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\Entity\PlayerBadge;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\Event\PlayerBadgeLost;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\Event\PlayerBadgeObtained;

class PlayerBadgeListener
{
    /** @var array<string, array<mixed>> */
    private array $changeSet = [];

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param PlayerBadge $playerBadge
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PlayerBadge $playerBadge, PreUpdateEventArgs $event): void
    {
        $this->changeSet = $event->getEntityChangeSet();
    }

    /**
     * @param PlayerBadge $playerBadge
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(PlayerBadge $playerBadge, LifecycleEventArgs $event): void
    {
        if ($playerBadge->getBadge()->isTypeMaster() && array_key_exists('endedAt', $this->changeSet)) {
            $this->eventDispatcher->dispatch(new PlayerBadgeLost($playerBadge));
        }
    }

    /**
     * @param PlayerBadge $playerBadge
     * @param LifecycleEventArgs $event
     */
    public function postPersist(PlayerBadge $playerBadge, LifecycleEventArgs $event): void
    {
        $this->eventDispatcher->dispatch(new PlayerBadgeObtained($playerBadge));
    }
}
