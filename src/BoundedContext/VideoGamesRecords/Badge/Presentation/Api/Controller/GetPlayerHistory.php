<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Badge\Presentation\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\Entity\Badge;
use App\BoundedContext\VideoGamesRecords\Badge\Infrastructure\Doctrine\Repository\PlayerBadgeRepository;

class GetPlayerHistory extends AbstractController
{
    public function __construct(
        private readonly PlayerBadgeRepository $playerBadgeRepository
    ) {
    }

    /**
     * @return array<\App\BoundedContext\VideoGamesRecords\Badge\Domain\Entity\PlayerBadge>
     */
    public function __invoke(Badge $badge): array
    {
        $qb = $this->playerBadgeRepository->createQueryBuilder('pb')
            ->select('pb', 'p', 'b')
            ->join('pb.player', 'p')
            ->join('pb.badge', 'b')
            ->where('pb.badge = :badge')
            ->setParameter('badge', $badge)
            ->orderBy('pb.createdAt', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
