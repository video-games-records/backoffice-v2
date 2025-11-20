<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\EventListener;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;

class PlayerListener
{
    /** @var array<string, array{0: mixed, 1: mixed}> */
    private array $changeSet = [];

    /**
     * @param Player             $player
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Player $player, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();
        $player->setStatus($em->getReference('App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerStatus', 1));
    }

    /**
     * @param Player $player
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Player $player, PreUpdateEventArgs $event): void
    {
        $this->changeSet = $event->getEntityChangeSet();
    }

    /**
     * @param Player             $player
     * @param LifecycleEventArgs $event
     * @return void
     * @throws Exception
     */
    public function postUpdate(Player $player, LifecycleEventArgs $event): void
    {
        if (array_key_exists('team', $this->changeSet)) {
            //@todo MAJ ranking team
        }
    }
}
