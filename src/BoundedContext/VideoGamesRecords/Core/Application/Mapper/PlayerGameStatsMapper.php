<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\Mapper;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Intl\Locale;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerGameStatsDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerGameStatItemDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Game\Response\GameMinimalDTO;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerGame;

class PlayerGameStatsMapper
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function toResponseDTO(Player $player): PlayerGameStatsDTO
    {
        $playerGames = $this->getPlayerGameStats($player);
        $stats = $this->getStatusPerGame($player);

        $playerGameItems = [];

        /** @var PlayerGame $playerGame */
        foreach ($playerGames as $playerGame) {
            $gameStatuses = $stats[$playerGame->getGame()->getId()] ?? [];

            // Map platforms
            $platforms = [];
            foreach ($playerGame->getGame()->getPlatforms() as $platform) {
                $platforms[] = [
                    'id' => $platform->getId(),
                    'name' => $platform->getName(),
                    'slug' => $platform->getSlug()
                ];
            }

            // Create Game DTO
            $gameDTO = new GameMinimalDTO(
                id: $playerGame->getGame()->getId() ?? 0,
                name: $playerGame->getGame()->getName(),
                slug: $playerGame->getGame()->getSlug(),
                platforms: $platforms
            );

            $playerGameItems[] = new PlayerGameStatItemDTO(
                game: $gameDTO,
                nbChart: $playerGame->getNbChart(),
                nbChartWithoutDlc: $playerGame->getNbChartWithoutDlc(),
                nbChartProven: $playerGame->getNbChartProven(),
                nbChartProvenWithoutDlc: $playerGame->getNbChartProvenWithoutDlc(),
                nbEqual: $playerGame->getNbEqual(),
                rankMedal: $playerGame->getRankMedal(),
                chartRank0: $playerGame->getChartRank0(),
                chartRank1: $playerGame->getChartRank1(),
                chartRank2: $playerGame->getChartRank2(),
                chartRank3: $playerGame->getChartRank3(),
                chartRank4: $playerGame->getChartRank4(),
                chartRank5: $playerGame->getChartRank5(),
                rankPointChart: $playerGame->getRankPointChart(),
                pointChart: $playerGame->getPointChart(),
                pointChartWithoutDlc: $playerGame->getPointChartWithoutDlc(),
                pointGame: $playerGame->getPointGame(),
                lastUpdate: $playerGame->getLastUpdate(),
                statuses: $gameStatuses
            );
        }

        return new PlayerGameStatsDTO($playerGameItems);
    }

    /**
     * Return data from player with game and platforms
     *
     * @param Player $player
     * @return array<PlayerGame>
     */
    private function getPlayerGameStats(Player $player): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('pg')
            ->from('App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerGame', 'pg')
            ->join('pg.game', 'g')
            ->addSelect('g')
            ->join('g.platforms', 'p')
            ->addSelect('p')
            ->where('pg.player = :player')
            ->setParameter('player', $player)
            ->orderBy('g.' . (Locale::getDefault() == 'fr' ? 'libGameFr' : 'libGameEn'), 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Player $player
     * @return array<int, array<int, array<string, mixed>>>
     */
    private function getStatusPerGame(Player $player): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('gam.id')
            ->from('App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game', 'gam')
            ->addSelect('pc.status as status')
            ->addSelect('COUNT(pc) as nb')
            ->innerJoin('gam.groups', 'grp')
            ->innerJoin('grp.charts', 'chr')
            ->innerJoin('chr.playerCharts', 'pc')
            ->where('pc.player = :player')
            ->setParameter('player', $player)
            ->groupBy('gam.id')
            ->addGroupBy('pc.status')
            ->orderBy('gam.id', 'ASC')
            ->addOrderBy('pc.status', 'ASC');

        $list = $qb->getQuery()->getResult(2);

        $games = [];
        foreach ($list as $row) {
            $idGame = $row['id'];
            if (!array_key_exists($idGame, $games)) {
                $games[$idGame] = [];
            }
            $games[$idGame][] = [
                'status' => $row['status'],
                'nb' => $row['nb'],
            ];
        }
        return $games;
    }
}
