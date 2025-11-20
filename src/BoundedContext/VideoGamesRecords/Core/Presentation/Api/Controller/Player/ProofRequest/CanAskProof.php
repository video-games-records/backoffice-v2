<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Player\ProofRequest;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BoundedContext\VideoGamesRecords\Proof\Application\DataProvider\CanAskProofProvider;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;

class CanAskProof extends AbstractController
{
    private CanAskProofProvider $canAskProofProvider;

    public function __construct(CanAskProofProvider $canAskProofProvider)
    {
        $this->canAskProofProvider = $canAskProofProvider;
    }

    /**
     * @param Player $player
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function __invoke(Player $player): bool
    {
        return $this->canAskProofProvider->load($player);
    }
}
