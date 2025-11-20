<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Badge\Presentation\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\Entity\Badge;
use App\BoundedContext\VideoGamesRecords\Badge\Infrastructure\Doctrine\Repository\TeamBadgeRepository;

class GetTeamHistory extends AbstractController
{
    public function __construct(
        private readonly TeamBadgeRepository $teamBadgeRepository
    ) {
    }

    /**
     * @return array<\App\BoundedContext\VideoGamesRecords\Badge\Domain\Entity\TeamBadge>
     */
    public function __invoke(Badge $badge): array
    {
        $qb = $this->teamBadgeRepository->createQueryBuilder('tb')
            ->select('tb', 't', 'b')
            ->join('tb.team', 't')
            ->join('tb.badge', 'b')
            ->where('tb.badge = :badge')
            ->setParameter('badge', $badge)
            ->orderBy('tb.createdAt', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
