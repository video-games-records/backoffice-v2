<?php

declare(strict_types=1);

namespace App\BoundedContext\Forum\Infrastructure\EventListener\Entity;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\BoundedContext\Forum\Domain\Entity\Message;
use App\BoundedContext\User\Domain\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

readonly class MessageListener
{
    public function __construct(
        private Security $security
    ) {
    }

    /**
     * @param Message       $message
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Message $message, LifecycleEventArgs $event): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $message->setUser($user);
        }

        $topic = $message->getTopic();
        $topic->setNbMessage($topic->getNbMessage() + 1);
        $topic->setLastMessage($message);
        $topic->setBoolArchive(false);
        $message->setPosition($topic->getNbMessage() + 1);

        $forum = $topic->getForum();
        $forum->setNbMessage($forum->getNbMessage() + 1);
        $forum->setLastMessage($message);
    }

    /**
     * @param Message $message
     * @param LifecycleEventArgs $event
     */
    public function preRemove(Message $message, LifecycleEventArgs $event): void
    {
        $topic = $message->getTopic();
        $topic->setNbMessage($topic->getNbMessage() - 1);

        $i = 1;
        foreach ($topic->getMessages() as $row) {
            $row->setPosition($i);
            $i++;
        }

        $forum = $topic->getForum();
        $forum->setNbMessage($forum->getNbMessage() - 1);
    }

    /**
     * @param Message $message
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function postRemove(Message $message, LifecycleEventArgs $event): void
    {
        $topic = $message->getTopic();
        $forum = $topic->getForum();
        $lastMessage = $topic->getMessages()->last();
        if ($message === $topic->getLastMessage()) {
            $topic->setLastMessage($lastMessage);
            $event->getObjectManager()->flush();
        }
        if ($message === $forum->getLastMessage()) {
            $forum->setLastMessage($lastMessage);
            $event->getObjectManager()->flush();
        }
    }
}
