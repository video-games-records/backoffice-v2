<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Game;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Contracts\Ranking\RankingProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DataProvider\Ranking\PlayerGameRankingProvider;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game;

class GetPlayerGameRankingMedals extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(
        #[Autowire(service: PlayerGameRankingProvider::class)]
        RankingProviderInterface $rankingProvider
    ) {
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(Game $game, Request $request): array
    {
        return $this->rankingProvider->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '5'),
                'idTeam' => $request->query->get('idTeam'),
                'user' => $this->getUser()
            ]
        );
    }
}
