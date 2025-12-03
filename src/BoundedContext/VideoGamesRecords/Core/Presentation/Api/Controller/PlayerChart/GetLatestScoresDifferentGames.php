<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\PlayerChart;

use ApiPlatform\Doctrine\Orm\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChart;

class GetLatestScoresDifferentGames extends AbstractController
{
    private EntityManagerInterface $em;
    private CacheInterface $cache;

    public function __construct(EntityManagerInterface $em, CacheInterface $cache)
    {
        $this->em = $em;
        $this->cache = $cache;
    }

    public function __invoke(Request $request): Paginator
    {
        $limit = $request->query->getInt('limit', 10);
        $days = $request->query->getInt('days', 30);

        $date = new \DateTime("-{$days} days");

        $queryBuilder = $this->em->createQueryBuilder()
            ->select('pc1')
            ->from(PlayerChart::class, 'pc1')
            ->join('pc1.chart', 'c')
            ->join('c.group', 'g')
            ->where('pc1.lastUpdate >= :date')
            ->andWhere('NOT EXISTS (
                SELECT 1 
                FROM App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChart pc2
                JOIN pc2.chart c2
                JOIN c2.group g2
                WHERE pc2.lastUpdate > pc1.lastUpdate
                AND g2.game = g.game
            )')
            ->orderBy('pc1.lastUpdate', 'DESC')
            ->setParameter('date', $date)
            ->setMaxResults($limit);

        $doctrinePaginator = new DoctrinePaginator($queryBuilder->getQuery());
        $doctrinePaginator->setUseOutputWalkers(false);

        return new Paginator($doctrinePaginator);
    }
}
