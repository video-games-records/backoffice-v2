<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Player\PlayerChart;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;

class GetStats extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Player $player
     * @param Request $request
     * @return mixed
     */
    public function __invoke(Player $player, Request $request): mixed
    {
        $idGame = $request->query->get('idGame');

        $qb = $this->em->createQueryBuilder()
            ->select('pc.status', 'COUNT(pc) as nb')
            ->from('App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChart', 'pc')
            ->where('pc.player = :player')
            ->setParameter('player', $player)
            ->groupBy('pc.status');

        if ($idGame !== null) {
            $qb->join('pc.chart', 'c')
                ->join('c.group', 'g')
                ->join('g.game', 'game')
                ->andWhere('game.id = :idGame')
                ->setParameter('idGame', (int) $idGame);
        }

        return $qb->getQuery()->getResult();
    }
}
