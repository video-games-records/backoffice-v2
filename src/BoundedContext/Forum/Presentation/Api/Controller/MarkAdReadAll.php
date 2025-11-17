<?php

declare(strict_types=1);

namespace App\BoundedContext\Forum\Presentation\Api\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\BoundedContext\Forum\Domain\Entity\ForumUserLastVisit;
use App\BoundedContext\Forum\Domain\Entity\TopicUserLastVisit;
use App\BoundedContext\User\Domain\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class MarkAdReadAll extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Marque tous les forums et topics comme lus pour l'utilisateur connecté
     */
    public function __invoke(): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], 401);
        }
        $now = new \DateTime();

        try {
            $this->em->beginTransaction();

            // 1. Mettre à jour toutes les visites existantes de forums
            $this->em->createQueryBuilder()
                ->update('App\BoundedContext\Forum\Domain\Entity\ForumUserLastVisit', 'fuv')
                ->set('fuv.lastVisitedAt', ':now')
                ->where('fuv.user = :user')
                ->setParameter('now', $now)
                ->setParameter('user', $user)
                ->getQuery()
                ->execute();

            // 2. Créer des visites pour les forums jamais visités qui ont des messages
            $forumsNeverVisited = $this->em->createQueryBuilder()
                ->select('f')
                ->from('App\BoundedContext\Forum\Domain\Entity\Forum', 'f')
                ->where('f.lastMessage IS NOT NULL')
                ->andWhere('f.id NOT IN (
                    SELECT IDENTITY(fuv.forum) 
                    FROM App\BoundedContext\Forum\Domain\Entity\ForumUserLastVisit fuv 
                    WHERE fuv.user = :user
                )')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();

            foreach ($forumsNeverVisited as $forum) {
                $visit = new ForumUserLastVisit();
                $visit->setUser($user);
                $visit->setForum($forum);
                $visit->setLastVisitedAt($now);
                $this->em->persist($visit);
            }

            // 3. Mettre à jour toutes les visites existantes de topics
            $this->em->createQueryBuilder()
                ->update('App\BoundedContext\Forum\Domain\Entity\TopicUserLastVisit', 'tuv')
                ->set('tuv.lastVisitedAt', ':now')
                ->where('tuv.user = :user')
                ->setParameter('now', $now)
                ->setParameter('user', $user)
                ->getQuery()
                ->execute();

            // 4. Créer des visites pour les topics jamais visités qui ont des messages
            $topicsNeverVisited = $this->em->createQueryBuilder()
                ->select('t')
                ->from('App\BoundedContext\Forum\Domain\Entity\Topic', 't')
                ->where('t.lastMessage IS NOT NULL')
                ->andWhere('t.id NOT IN (
                    SELECT IDENTITY(tuv.topic) 
                    FROM App\BoundedContext\Forum\Domain\Entity\TopicUserLastVisit tuv 
                    WHERE tuv.user = :user
                )')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();

            foreach ($topicsNeverVisited as $topic) {
                $visit = new TopicUserLastVisit();
                $visit->setUser($user);
                $visit->setTopic($topic);
                $visit->setLastVisitedAt($now);
                $this->em->persist($visit);
            }

            $this->em->flush();
            $this->em->commit();

            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            $this->em->rollback();
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
