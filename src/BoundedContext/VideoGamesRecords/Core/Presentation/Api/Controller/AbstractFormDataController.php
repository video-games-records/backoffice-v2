<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChart;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChartLib;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChartStatus;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Security\UserProvider;

abstract class AbstractFormDataController extends AbstractController
{
    protected UserProvider $userProvider;
    protected EntityManagerInterface $em;
    protected Game $game;

    public function __construct(UserProvider $userProvider, EntityManagerInterface $em)
    {
        $this->userProvider = $userProvider;
        $this->em = $em;
    }

    protected function setScores(Collection $charts, Player $player): Collection
    {
        $platforms = $this->game->getPlatforms();
        foreach ($charts as $chart) {
            if (count($chart->getPlayerCharts()) == 0) {
                $playerChart = new PlayerChart();
                $playerChart->setId(-1);
                $playerChart->setChart($chart);
                $playerChart->setPlayer($player);
                $playerChart->setLastUpdate(new \DateTime());
                $playerChart->setStatus($this->em->getRepository(PlayerChartStatus::class)->findOneBy(['id' => 1]));
                if (count($platforms) == 1) {
                    $playerChart->setPlatform($platforms[0]);
                }
                foreach ($chart->getLibs() as $lib) {
                    $playerChartLib = new PlayerChartLib();
                    $playerChartLib->setId(-1);
                    $playerChartLib->setLibChart($lib);
                    $playerChart->addLib($playerChartLib);
                }
                $chart->addPlayerChart($playerChart);
            } else {
                $playerCharts = $chart->getPlayerCharts();
                $playerCharts[0]->setLastUpdate(new \DateTime());
            }
        }
        return $charts;
    }
}
