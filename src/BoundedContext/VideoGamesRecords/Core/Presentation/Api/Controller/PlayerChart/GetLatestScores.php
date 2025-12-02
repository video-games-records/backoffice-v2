<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\PlayerChart;

use ApiPlatform\Doctrine\Orm\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChart;

class GetLatestScores extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(Request $request): Paginator
    {
        $days = (int) $request->query->get('days', 7);
        $page = $request->query->getInt('page', 1);
        $itemsPerPage = $request->query->getInt('itemsPerPage', 10);

        $queryBuilder = $this->em->createQueryBuilder()
            ->select('pc')
            ->from(PlayerChart::class, 'pc')
            ->where('pc.lastUpdate >= :date')
            ->andWhere('pc.id > 0')
            ->orderBy('pc.lastUpdate', 'DESC')
            ->setParameter('date', new \DateTime('-' . $days . ' days'))
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage);

        $doctrinePaginator = new DoctrinePaginator($queryBuilder->getQuery());
        $doctrinePaginator->setUseOutputWalkers(false);

        return new Paginator($doctrinePaginator);
    }
}
