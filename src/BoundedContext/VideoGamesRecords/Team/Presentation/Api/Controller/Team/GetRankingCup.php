<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Team\Presentation\Api\Controller\Team;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Team\Application\DataProvider\Ranking\TeamRankingProvider;

class GetRankingCup extends AbstractController
{
    private TeamRankingProvider $teamRankingProvider;

    public function __construct(TeamRankingProvider $teamRankingProvider)
    {
        $this->teamRankingProvider = $teamRankingProvider;
    }

    /**
     * @param Request $request
     * @return array<string, mixed>
     * @throws ORMException
     */
    public function __invoke(Request $request): array
    {
        return $this->teamRankingProvider->getRankingCup(
            [
                'maxRank' => $request->query->get('maxRank', '5'),
            ]
        );
    }
}
