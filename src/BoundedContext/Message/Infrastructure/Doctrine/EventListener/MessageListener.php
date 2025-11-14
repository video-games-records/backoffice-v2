<?php

declare(strict_types=1);

namespace App\BoundedContext\Message\Infrastructure\Doctrine\EventListener;

use App\BoundedContext\Message\Domain\Entity\Message;
use App\BoundedContext\User\Domain\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

readonly class MessageListener
{
    public function __construct(private Security $security)
    {
    }

    public function prePersist(Message $message): void
    {
        if (null === $message->getSender()) {
            $user = $this->security->getUser();
            if ($user instanceof User) {
                $message->setSender($user);
            }
        }
    }
}
