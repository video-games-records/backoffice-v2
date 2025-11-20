<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Chart\Player;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BoundedContext\VideoGamesRecords\Core\Application\DataProvider\Ranking\PlayerChartRankingProvider;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Chart;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Tools\ScoreTools;

class GetRankingDisabled extends AbstractController
{
    private PlayerChartRankingProvider $playerChartRankingProvider;

    public function __construct(PlayerChartRankingProvider $playerChartRankingProvider)
    {
        $this->playerChartRankingProvider = $playerChartRankingProvider;
    }

    /**
     * @param Chart    $chart
     * @return array<string, mixed>
     */
    public function __invoke(Chart $chart): array
    {
        $ranking = $this->playerChartRankingProvider->getRankingDisabled($chart);

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
