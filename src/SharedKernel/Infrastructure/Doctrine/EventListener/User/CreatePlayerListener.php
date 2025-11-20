<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Doctrine\EventListener\User;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use App\BoundedContext\User\Domain\Entity\User;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\Entity\PlayerBadge;

#[AsEntityListener(event: Events::postPersist, method: 'createPlayer', entity: User::class)]
readonly class CreatePlayerListener
{
    public const int GROUP_PLAYER = 2;
    public const int BADGE_REGISTER = 1;

    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function createPlayer(User $user): void
    {
        // Role Player
        $group = $this->em->getReference('App\BoundedContext\User\Domain\Entity\Group', self::GROUP_PLAYER);
        $user->addGroup($group);

        // Player
        $player = new Player();
        $player->setId($user->getId());
        $player->setUserId($user->getId());
        $player->setPseudo($user->getUsername());

        $status = $this->em->getReference('App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerStatus', 1);
        $player->setStatus($status);

        $this->em->persist($player);

        // Register Badge
        $badge = $this->em->getReference('App\BoundedContext\VideoGamesRecords\Badge\Domain\Entity\Badge', self::BADGE_REGISTER);
        $playerBadge = new PlayerBadge();
        $playerBadge->setPlayer($player);
        $playerBadge->setBadge($badge);
        $this->em->persist($playerBadge);

        $this->em->flush();
    }
}
