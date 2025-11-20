<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Team\Presentation\Api\Controller\Chart\Team;

use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Chart;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Contracts\Ranking\RankingProviderInterface;
use App\BoundedContext\VideoGamesRecords\Team\Application\DataProvider\Ranking\TeamChartRankingProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;

class GetRankingPoints extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(
        #[Autowire(service: TeamChartRankingProvider::class)]
        RankingProviderInterface $rankingProvider
    ) {
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @param Chart   $chart
     * @param Request $request
     * @return array<string, mixed>
     */
    public function __invoke(Chart $chart, Request $request): array
    {
        return $this->rankingProvider->getRankingPoints(
            $chart->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '5'),
                'user' => $this->getUser()
            ]
        );
    }
}
