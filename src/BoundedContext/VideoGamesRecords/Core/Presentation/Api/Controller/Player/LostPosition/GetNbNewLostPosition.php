<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Player\LostPosition;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Core\Application\Manager\LostPositionManager;

class GetNbNewLostPosition extends AbstractController
{
    private LostPositionManager $lostPositionManager;

    public function __construct(LostPositionManager $lostPositionManager)
    {
        $this->lostPositionManager = $lostPositionManager;
    }

    /**
     * @param Player $player
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function __invoke(Player $player): int
    {
        return $this->lostPositionManager->getNbNewLostPosition($player);
    }
}
