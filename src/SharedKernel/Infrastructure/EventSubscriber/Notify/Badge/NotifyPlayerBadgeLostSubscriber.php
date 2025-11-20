<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\EventSubscriber\Notify\Badge;

use App\SharedKernel\Infrastructure\EventSubscriber\Notify\AbstractNotifySubscriberInterface;
use Doctrine\ORM\Exception\ORMException;
use App\BoundedContext\User\Domain\Entity\User;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\Event\PlayerBadgeLost;

final class NotifyPlayerBadgeLostSubscriber extends AbstractNotifySubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            PlayerBadgeLost::class => 'sendMessage',
        ];
    }

    /**
     * @param PlayerBadgeLost $event
     * @throws ORMException
     */
    public function sendMessage(PlayerBadgeLost $event): void
    {
        $playerBadge = $event->getPlayerBadge();
        $game = $playerBadge->getBadge()->getGame();
        $this->messageBuilder
            ->setSender($this->getDefaultSender())
            ->setType('VGR_PLAYER_BADGE');


        // Send MP
        /** @var User $recipient */
        $recipient = $this->em->getRepository('App\BoundedContext\User\Domain\Entity\User')
            ->find($playerBadge->getPlayer()->getUserId());
        $url = '/' . $recipient->getLanguage() . '/' . $game->getUrl();
        $this->messageBuilder
            ->setObject(
                $this->translator->trans(
                    'player_badge_lost.object',
                    [],
                    'VgrCoreNotification',
                    $recipient->getLanguage()
                )
            )
            ->setMessage(
                sprintf(
                    $this->translator->trans(
                        'player_badge_lost.message',
                        [],
                        'VgrCoreNotification',
                        $recipient->getLanguage()
                    ),
                    $recipient->getUsername(),
                    $url,
                    $game->getName($recipient->getLanguage())
                )
            )
            ->setRecipient($recipient)
            ->send();
    }
}
