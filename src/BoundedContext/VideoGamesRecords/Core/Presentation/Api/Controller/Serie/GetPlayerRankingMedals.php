<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Serie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Contracts\Ranking\RankingProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DataProvider\Ranking\PlayerSerieRankingProvider;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Serie;

class GetPlayerRankingMedals extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(
        #[Autowire(service: PlayerSerieRankingProvider::class)]
        RankingProviderInterface $rankingProvider
    ) {
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(Serie $serie, Request $request): array
    {
        return $this->rankingProvider->getRankingMedals(
            $serie->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '100'),
                'limit' => $request->query->get('limit'),
                'user' => $this->getUser()
            ]
        );
    }
}
