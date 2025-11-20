<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Chart\Player;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Core\Application\DataProvider\Ranking\PlayerChartRankingProvider;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Chart;

class GetRankingPoints extends AbstractController
{
    private PlayerChartRankingProvider $playerChartRankingProvider;

    public function __construct(PlayerChartRankingProvider $playerChartRankingProvider)
    {
        $this->playerChartRankingProvider = $playerChartRankingProvider;
    }

    /**
     * @param Chart   $chart
     * @param Request $request
     * @return array<string, mixed>
     * @throws ORMException
     */
    public function __invoke(Chart $chart, Request $request): array
    {
        return $this->playerChartRankingProvider->getRankingPoints(
            $chart->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '5'),
                'idTeam' => $request->query->get('idTeam'),
                'user' => $this->getUser()
            ]
        );
    }
}
