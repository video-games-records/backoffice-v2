<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs as BaseLifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\Entity\Badge;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Serie;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\ValueObject\BadgeType;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerSerieRank;
use App\BoundedContext\VideoGamesRecords\Core\Domain\ValueObject\SerieStatus;

class SerieListener
{
    /** @var array<string, array{0: mixed, 1: mixed}> */
    private array $changeSet = [];

    public function __construct(private MessageBusInterface $bus, private RequestStack $requestStack)
    {
    }

    /**
     * @param Serie                   $serie
     * @param BaseLifecycleEventArgs $event
     */
    public function prePersist(Serie $serie, BaseLifecycleEventArgs $event): void
    {
        $badge = new Badge();
        $badge->setType(BadgeType::SERIE);
        $badge->setPicture('default.gif');
        $serie->setBadge($badge);
    }

    /**
     * @param Serie              $serie
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Serie $serie, PreUpdateEventArgs $event): void
    {
        $this->changeSet = $event->getEntityChangeSet();
    }

    /**
     * @param Serie $serie
     * @param BaseLifecycleEventArgs $event
     * @throws ExceptionInterface
     */
    public function postUpdate(Serie $serie, BaseLifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();

        if (
            array_key_exists('status', $this->changeSet)
            && $this->changeSet['status'][1] == SerieStatus::ACTIVE
        ) {
            $this->bus->dispatch(new UpdatePlayerSerieRank($serie->getId()));
        }

        $em->flush();
    }

    /**
     * @param Serie $serie
     * @param LifecycleEventArgs $event
     */
    public function postLoad(Serie $serie, LifecycleEventArgs $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $serie->setCurrentLocale($request->getLocale());
        }
    }
}
