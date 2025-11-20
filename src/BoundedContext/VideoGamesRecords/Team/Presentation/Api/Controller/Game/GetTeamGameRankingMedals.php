<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Team\Presentation\Api\Controller\Game;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Contracts\Ranking\RankingProviderInterface;
use App\BoundedContext\VideoGamesRecords\Team\Application\DataProvider\Ranking\TeamGameRankingProvider;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game;

class GetTeamGameRankingMedals extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(
        #[Autowire(service: TeamGameRankingProvider::class)]
        RankingProviderInterface $rankingProvider
    ) {
        $this->rankingProvider = $rankingProvider;
    }

    public function __invoke(Game $game, Request $request): array
    {
        return $this->rankingProvider->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '5'),
                'user' => $this->getUser()
            ]
        );
    }
}
