<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Proof\Infrastructure\Doctrine\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\BoundedContext\VideoGamesRecords\Proof\Domain\Entity\VideoComment;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Security\UserProvider;

class VideoCommentListener
{
    private UserProvider $userProvider;

    /**
     * @param UserProvider $userProvider
     */
    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param VideoComment       $comment
     * @param LifecycleEventArgs $event
     */
    public function prePersist(VideoComment $comment, LifecycleEventArgs $event): void
    {
        $comment->setPlayer($this->userProvider->getPlayer());
        $comment->getVideo()->setNbComment($comment->getVideo()->getNbComment() + 1);
    }


    /**
     * @param VideoComment       $comment
     * @param LifecycleEventArgs $event
     */
    public function preRemove(VideoComment $comment, LifecycleEventArgs $event): void
    {
        $comment->getVideo()->setNbComment($comment->getVideo()->getNbComment() - 1);
    }
}
