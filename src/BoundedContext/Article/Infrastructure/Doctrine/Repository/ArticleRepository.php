<?php

declare(strict_types=1);

namespace App\BoundedContext\Article\Infrastructure\Doctrine\Repository;

use App\BoundedContext\Article\Domain\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }
}
