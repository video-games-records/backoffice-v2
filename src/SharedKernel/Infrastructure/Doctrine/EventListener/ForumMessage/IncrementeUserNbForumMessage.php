<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Doctrine\EventListener\ForumMessage;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use App\BoundedContext\Forum\Domain\Entity\Message;

#[AsEntityListener(event: Events::prePersist, method: 'incrementeUserNbForumMessage', entity: Message::class)]
readonly class IncrementeUserNbForumMessage
{
    public function incrementeUserNbForumMessage(Message $message): void
    {
        $user = $message->getUser();
        $user->setNbForumMessage($user->getNbForumMessage() + 1);
    }
}
