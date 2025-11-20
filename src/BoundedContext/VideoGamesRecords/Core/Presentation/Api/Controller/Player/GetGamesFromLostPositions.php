<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Player;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;

#[AsController]
class GetGamesFromLostPositions extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(Player $player): array
    {
        $query = $this->entityManager->createQuery('
            SELECT DISTINCT g
            FROM App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game g
            INNER JOIN App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Group gr WITH gr.game = g
            INNER JOIN App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Chart c WITH c.group = gr
            INNER JOIN App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\LostPosition lp WITH lp.chart = c
            WHERE lp.player = :player
            ORDER BY g.libGameEn ASC
        ');

        $query->setParameter('player', $player);
        return $query->getResult();
    }
}
