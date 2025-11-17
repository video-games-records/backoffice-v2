<?php

declare(strict_types=1);

namespace App\BoundedContext\Forum\Presentation\Api\Controller;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use App\BoundedContext\Forum\Domain\ValueObject\ForumStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetHome extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function __invoke(): mixed
    {
        $user = $this->getUser();

        $queryBuilder = $this->em->createQueryBuilder()
            ->from('App\BoundedContext\Forum\Domain\Entity\Category', 'c')
            ->select('c')
            ->join('c.forums', 'f')
            ->leftJoin('f.lastMessage', 'm')
            ->leftJoin('m.user', 'u')
            ->addSelect('f')
            ->addSelect('m')
            ->addSelect('u');

        if ($user !== null) {
            $queryBuilder
                ->leftJoin(
                    'f.userLastVisits',
                    'fuv',
                    'WITH',
                    'fuv.user = :user'
                )
                ->addSelect('fuv')
                ->where(
                    $queryBuilder->expr()->orX(
                        'f.status = :status1',
                        '(f.status = :status2) AND (f.role IN (:roles))'
                    )
                )
                ->setParameter('status1', ForumStatus::PUBLIC)
                ->setParameter('status2', ForumStatus::PRIVATE)
                ->setParameter('user', $user)
                ->setParameter('roles', $user->getRoles());
        } else {
            $queryBuilder->where('f.status = :status')
                ->setParameter('status', ForumStatus::PUBLIC);
        }

        $queryBuilder->andWhere('c.displayOnHome = :displayOnHome')
            ->setParameter('displayOnHome', true);

        $queryBuilder->orderBy('c.position', 'ASC')
            ->addOrderBy('f.position', 'ASC');

        $categories = $queryBuilder->getQuery()->getResult();

        if ($user !== null) {
            $this->enrichWithReadStatus($categories, $user);
        }

        return $categories;
    }

    /**
     * @param array<mixed> $categories
     * @param mixed $user
     */
    private function enrichWithReadStatus(array $categories, $user): void
    {
        $forumIds = [];
        $forumsById = [];

        foreach ($categories as $category) {
            foreach ($category->getForums() as $forum) {
                $forumIds[] = $forum->getId();
                $forumsById[$forum->getId()] = $forum;
            }
        }

        if (empty($forumIds)) {
            return;
        }

        $unreadCounts = $this->getUnreadTopicsCountByForum($user, $forumIds);

        foreach ($forumsById as $forumId => $forum) {
            $forum->unreadTopicsCount = $unreadCounts[$forumId] ?? 0;
            $forum->isUnread = $forum->unreadTopicsCount > 0;
        }
    }

    /**
     * @param mixed $user
     * @param array<mixed> $forumIds
     * @return array<mixed>
     */
    private function getUnreadTopicsCountByForum($user, array $forumIds): array
    {
        $visitedUnreadQuery = $this->em->createQueryBuilder()
            ->select('IDENTITY(t.forum) as forum_id, COUNT(t.id) as unread_count')
            ->from('App\BoundedContext\Forum\Domain\Entity\TopicUserLastVisit', 'tuv')
            ->join('tuv.topic', 't')
            ->join('t.lastMessage', 'lm')
            ->where('t.forum IN (:forumIds)')
            ->andWhere('tuv.user = :user')
            ->andWhere('lm.createdAt > tuv.lastVisitedAt')
            ->groupBy('t.forum')
            ->setParameter('forumIds', $forumIds)
            ->setParameter('user', $user);

        $visitedUnread = $visitedUnreadQuery->getQuery()->getResult();

        $neverVisitedQuery = $this->em->createQueryBuilder()
            ->select('IDENTITY(t.forum) as forum_id, COUNT(t.id) as unread_count')
            ->from('App\BoundedContext\Forum\Domain\Entity\Topic', 't')
            ->where('t.forum IN (:forumIds)')
            ->andWhere('t.lastMessage IS NOT NULL')
            ->andWhere('t.id NOT IN (
                SELECT IDENTITY(tuv2.topic) 
                FROM App\BoundedContext\Forum\Domain\Entity\TopicUserLastVisit tuv2 
                WHERE tuv2.user = :user
            )')
            ->groupBy('t.forum')
            ->setParameter('forumIds', $forumIds)
            ->setParameter('user', $user);

        $neverVisited = $neverVisitedQuery->getQuery()->getResult();

        $result = [];

        foreach ($forumIds as $forumId) {
            $result[$forumId] = 0;
        }

        foreach ($visitedUnread as $row) {
            $result[(int) $row['forum_id']] += (int) $row['unread_count'];
        }

        foreach ($neverVisited as $row) {
            $result[(int) $row['forum_id']] += (int) $row['unread_count'];
        }

        return $result;
    }
}
