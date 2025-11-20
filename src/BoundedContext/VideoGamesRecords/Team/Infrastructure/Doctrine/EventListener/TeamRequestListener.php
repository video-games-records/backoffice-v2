<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Team\Infrastructure\Doctrine\EventListener;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\BoundedContext\VideoGamesRecords\Team\Domain\Entity\Team;
use App\BoundedContext\VideoGamesRecords\Team\Domain\Entity\TeamRequest;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Security\UserProvider;

class TeamRequestListener
{
    private UserProvider $userProvider;

    /**
     * @param UserProvider $userProvider
     */
    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }


    /**
     * @param TeamRequest $teamRequest
     * @param LifecycleEventArgs $event
     * @return void
     * @throws ORMException
     */
    public function prePersist(TeamRequest $teamRequest, LifecycleEventArgs $event): void
    {
        $teamRequest->setPlayer($this->userProvider->getPlayer());
    }

    /**
     * @param TeamRequest        $teamRequest
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(TeamRequest $teamRequest, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();
        if ($teamRequest->getTeamRequestStatus()->isAccepted()) {
            $player = $teamRequest->getPlayer();
            $player->setTeam($teamRequest->getTeam());
            $em->flush();
        }
    }
}
