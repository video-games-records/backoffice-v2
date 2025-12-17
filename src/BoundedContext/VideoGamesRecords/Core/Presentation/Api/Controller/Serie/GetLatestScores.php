<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Serie;

use ApiPlatform\Doctrine\Orm\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChart;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Serie;

class GetLatestScores extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(Serie $serie, Request $request): Paginator
    {
        $limit = min((int) $request->query->get('limit', 50), 100);

        $queryBuilder = $this->em->createQueryBuilder()
            ->select('pc')
            ->from(PlayerChart::class, 'pc')
            ->join('pc.chart', 'c')
            ->join('c.group', 'gr')
            ->join('gr.game', 'g')
            ->where('g.serie = :serie')
            ->setParameter('serie', $serie)
            ->orderBy('pc.lastUpdate', 'DESC')
            ->setMaxResults($limit);


        $doctrinePaginator = new DoctrinePaginator($queryBuilder->getQuery());
        $doctrinePaginator->setUseOutputWalkers(false);

        return new Paginator($doctrinePaginator);
    }
}
