<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\PlayerChart;

use ApiPlatform\Doctrine\Orm\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChart;

class GetLatestScoresDifferentGames extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(Request $request): Paginator
    {
        $limit = $request->query->getInt('limit', 10);
        $days = $request->query->getInt('days', 30);

        $date = new \DateTime("-{$days} days");

        // Utilisation d'une sous-requête avec MAX pour éviter NOT EXISTS - optimisation principale
        $subQuery = $this->em->createQueryBuilder()
            ->select('MAX(pc_sub.lastUpdate)')
            ->from(PlayerChart::class, 'pc_sub')
            ->join('pc_sub.chart', 'c_sub')
            ->join('c_sub.group', 'g_sub')
            ->where('g_sub.game = g.game')
            ->andWhere('pc_sub.lastUpdate >= :date')
            ->getDQL();

        $queryBuilder = $this->em->createQueryBuilder()
            ->select('pc')
            ->from(PlayerChart::class, 'pc')
            ->join('pc.chart', 'c')
            ->join('c.group', 'g')
            ->where('pc.lastUpdate >= :date')
            ->andWhere("pc.lastUpdate = ({$subQuery})")
            ->orderBy('pc.lastUpdate', 'DESC')
            ->setParameter('date', $date)
            ->setMaxResults($limit);

        $doctrinePaginator = new DoctrinePaginator($queryBuilder->getQuery());
        $doctrinePaginator->setUseOutputWalkers(false);

        return new Paginator($doctrinePaginator);
    }
}
