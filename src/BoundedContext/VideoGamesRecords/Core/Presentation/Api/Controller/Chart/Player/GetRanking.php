<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Chart\Player;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Core\Application\DataProvider\Ranking\PlayerChartRankingProvider;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Chart;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Tools\ScoreTools;

class GetRanking extends AbstractController
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
        $ranking = $this->playerChartRankingProvider->getRanking(
            $chart,
            [
                'maxRank' => $request->query->get('maxRank', '1000'),
                'user' => $this->getUser()
            ]
        );

        for ($i = 0; $i <= count($ranking) - 1; $i++) {
            foreach ($chart->getLibs() as $lib) {
                $key = $lib->getId();
                // format value
                $ranking[$i]['values'][] = ScoreTools::formatScore(
                    $ranking[$i]["value_$key"],
                    $lib->getType()->getMask()
                );
            }
        }
        return $ranking;
    }
}
