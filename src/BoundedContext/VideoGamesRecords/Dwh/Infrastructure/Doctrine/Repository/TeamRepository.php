<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Dwh\Infrastructure\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\BoundedContext\VideoGamesRecords\Dwh\Domain\Entity\Team;

class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }
}
