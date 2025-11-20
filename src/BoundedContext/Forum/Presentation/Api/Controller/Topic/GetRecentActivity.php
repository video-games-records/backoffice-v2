<?php

declare(strict_types=1);

namespace App\BoundedContext\Forum\Presentation\Api\Controller\Topic;

use Doctrine\ORM\EntityManagerInterface;
use App\BoundedContext\Forum\Domain\ValueObject\ForumStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetRecentActivity extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->from('App\BoundedContext\Forum\Domain\Entity\Topic', 't')
            ->select('t')
            ->join('t.forum', 'f')
            ->join('t.lastMessage', 'm')
            ->join('m.user', 'u')
            ->join('t.type', 'tt')
            ->addSelect('f')
            ->addSelect('m')
            ->addSelect('u')
            ->addSelect('tt')
            ->where('f.status = :status')
            ->andWhere('t.boolArchive = :archived')
            ->setParameter('status', ForumStatus::PUBLIC)
            ->setParameter('archived', false)
            ->orderBy('t.lastMessage', 'DESC')
            ->setMaxResults(50); // Limiter à 50 topics les plus récents

        return $queryBuilder->getQuery()->getResult();
    }
}
