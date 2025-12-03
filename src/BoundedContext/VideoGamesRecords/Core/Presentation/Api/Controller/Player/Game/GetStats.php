<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Player\Game;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Intl\Locale;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerGame;

class GetStats extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Player    $player
     * @return array<string, mixed>
     */
    public function __invoke(Player $player): array
    {
        $playerGames = $this->getPlayerGameStats($player);
        $stats = $this->getStatusPerGame($player);

        /** @var PlayerGame $playerGame */
        foreach ($playerGames as $playerGame) {
            if (isset($stats[$playerGame->getGame()->getId()])) {
                $playerGame->setStatuses($stats[$playerGame->getGame()->getId()]);
            }
        }
        return $playerGames;
    }

     /**
     * Return data from player with game and platforms
     *
     * @param $player
     * @return array<string, mixed>
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
     * @param $player
     * @return array<string, mixed>
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
