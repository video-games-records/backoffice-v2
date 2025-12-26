<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Team\Presentation\Api\Controller\Team;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BoundedContext\VideoGamesRecords\Team\Domain\Entity\Team;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\ValueObject\BadgeType;
use App\BoundedContext\VideoGamesRecords\Badge\Infrastructure\Doctrine\Repository\TeamBadgeRepository;

class GetBadges extends AbstractController
{
    public function __construct(
        private readonly TeamBadgeRepository $teamBadgeRepository
    ) {
    }

    /** @return array<string, mixed> */
    public function __invoke(Team $team): array
    {
        $result = [];

        $result['master'] = $this->teamBadgeRepository->findByTeamAndType(
            $team,
            BadgeType::MASTER->value,
            ['tb.mbOrder' => 'ASC']
        );

        $result['serie'] = $this->teamBadgeRepository->findByTeamAndType(
            $team,
            BadgeType::SERIE->value,
            ['tb.createdAt' => 'ASC']
        );

        return $result;
    }
}
