<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Dwh\Infrastructure\Doctrine\Repository;

use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use App\BoundedContext\VideoGamesRecords\Dwh\Domain\Entity\Player;

class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    /**
     * @param DateTime $begin
     * @param DateTime $end
     * @param integer  $limit
     * @return array<array<string, mixed>>
     */
    public function getTop(DateTime $begin, DateTime $end, int $limit = 20): array
    {
        $query = $this->_em->createQuery("
            SELECT p.id,
                   SUM(p.nbPostDay) as nb      
            FROM App\BoundedContext\VideoGamesRecords\Dwh\Domain\Entity\Player p
            WHERE p.date BETWEEN :begin AND :end
            GROUP BY p.id
            HAVING nb > 0
            ORDER BY nb DESC");

        $query->setParameter('begin', $begin);
        $query->setParameter('end', $end);
        $query->setMaxResults($limit);

        return $query->getArrayResult();
    }

    /**
     * @param DateTime $begin
     * @param DateTime $end
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotalNbPlayer(DateTime $begin, DateTime $end): mixed
    {
        $query = $this->_em->createQuery("
            SELECT COUNT(DISTINCT(p.id)) as nb
            FROM App\BoundedContext\VideoGamesRecords\Dwh\Domain\Entity\Player p
            WHERE p.date BETWEEN :begin AND :end
            AND p.nbPostDay > 0");

        $query->setParameter('begin', $begin);
        $query->setParameter('end', $end);

        return $query->getSingleScalarResult();
    }

    /**
     * @param DateTime $begin
     * @param DateTime $end
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotalNbPostDay(DateTime $begin, DateTime $end): mixed
    {
        $query = $this->_em->createQuery("
            SELECT SUM(p.nbPostDay) as nb
            FROM App\BoundedContext\VideoGamesRecords\Dwh\Domain\Entity\Player p
            WHERE p.date BETWEEN :begin AND :end");

        $query->setParameter('begin', $begin);
        $query->setParameter('end', $end);

        return $query->getSingleScalarResult();
    }
}
