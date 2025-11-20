<?php


declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\EventSubscriber\Discord;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Psr\Log\LoggerInterface;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\Entity\Badge;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\Event\PlayerBadgeObtained;

final readonly class PlayerMasterBadgeDiscordSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ChatterInterface $chatter,
        private ?LoggerInterface $logger = null
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PlayerBadgeObtained::class => 'onPlayerBadgeObtained',
        ];
    }

    /**
     * @param PlayerBadgeObtained $event
     */
    public function onPlayerBadgeObtained(PlayerBadgeObtained $event): void
    {
        try {
            $playerBadge = $event->getPlayerBadge();
            $badge = $playerBadge->getBadge();
            $player = $playerBadge->getPlayer();
            $game = $badge->getGame();

            if (!$badge->isTypeMaster()) {
                return;
            }

            $this->sendDiscordNotification($player, $game, $badge);
        } catch (\Exception $e) {
            $this->logger?->error('Error sending Discord notification for player Master badge', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function sendDiscordNotification(Player $player, Game $game, Badge $badge): void
    {
        $message = sprintf(
            "🏆 **NEW MASTER BADGE ACHIEVED!** 🏆\n\n" .
            "**Player:** %s\n" .
            "**Game:** %s\n" .
            "Congratulations! 🎉",
            $player->getPseudo(),
            $game->getLibGameEn()
        );

        $chatMessage = new ChatMessage($message);
        $this->chatter->send($chatMessage);

        $this->logger?->info('Discord notification sent for player Master badge', [
            'player_id' => $player->getId(),
            'player_pseudo' => $player->getPseudo(),
            'game_id' => $game->getId(),
            'badge_id' => $badge->getId()
        ]);
    }
}
