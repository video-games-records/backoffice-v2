<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Badge\Application\Mapper;

use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerBadgesDTO;
use App\BoundedContext\VideoGamesRecords\Badge\Application\DTO\Response\PlayerBadgeDTO;
use App\BoundedContext\VideoGamesRecords\Badge\Application\DTO\Response\BadgeDTO;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\ValueObject\BadgeType;
use App\BoundedContext\VideoGamesRecords\Badge\Infrastructure\Doctrine\Repository\PlayerBadgeRepository;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\Entity\PlayerBadge;

class PlayerBadgesMapper
{
    public function __construct(
        private PlayerBadgeRepository $playerBadgeRepository
    ) {
    }

    public function toResponseDTO(Player $player): PlayerBadgesDTO
    {
        return new PlayerBadgesDTO(
            special: $this->mapPlayerBadges($this->playerBadgeRepository->findByPlayerAndType(
                $player,
                BadgeType::getSpecialBadgeValues(),
                ['pb.createdAt' => 'ASC']
            )),
            connexion: $this->mapPlayerBadges($this->playerBadgeRepository->findByPlayerAndType(
                $player,
                BadgeType::CONNEXION->value,
                ['b.value' => 'ASC']
            )),
            forum: $this->mapPlayerBadges($this->playerBadgeRepository->findByPlayerAndType(
                $player,
                BadgeType::FORUM->value,
                ['b.value' => 'ASC']
            )),
            don: $this->mapPlayerBadges($this->playerBadgeRepository->findByPlayerAndType(
                $player,
                BadgeType::DON->value,
                ['b.value' => 'ASC']
            )),
            vgr_chart: $this->mapPlayerBadges($this->playerBadgeRepository->findByPlayerAndType(
                $player,
                BadgeType::VGR_CHART->value,
                ['b.value' => 'ASC']
            )),
            vgr_proof: $this->mapPlayerBadges($this->playerBadgeRepository->findByPlayerAndType(
                $player,
                BadgeType::VGR_PROOF->value,
                ['b.value' => 'ASC']
            )),
            master: $this->mapPlayerBadges($this->playerBadgeRepository->findByPlayerAndType(
                $player,
                BadgeType::MASTER->value,
                ['pb.mbOrder' => 'ASC']
            )),
            platform: $this->mapPlayerBadges($this->playerBadgeRepository->findByPlayerAndType(
                $player,
                BadgeType::PLATFORM->value,
                ['pb.createdAt' => 'ASC']
            )),
            serie: $this->mapPlayerBadges($this->playerBadgeRepository->findByPlayerAndType(
                $player,
                BadgeType::SERIE->value,
                ['pb.createdAt' => 'ASC']
            )),
        );
    }

    /**
     * @param array<PlayerBadge> $playerBadges
     * @return array<PlayerBadgeDTO>
     */
    private function mapPlayerBadges(array $playerBadges): array
    {
        return array_map(
            fn(PlayerBadge $playerBadge) => new PlayerBadgeDTO(
                id: $playerBadge->getId() ?? 0,
                badge: $this->mapBadge($playerBadge),
                createdAt: $playerBadge->getCreatedAt() ?? new \DateTime(),
                endedAt: $playerBadge->getEndedAt(),
                mbOrder: $playerBadge->getMbOrder()
            ),
            $playerBadges
        );
    }

    private function mapBadge(PlayerBadge $playerBadge): BadgeDTO
    {
        $badge = $playerBadge->getBadge();

        // Map related entities according to badge type
        $game = null;
        $serie = null;
        $platform = null;

        if ($badge->getGame() !== null) {
            $game = [
                'id' => $badge->getGame()->getId(),
                'name' => $badge->getGame()->getName(),
                'slug' => $badge->getGame()->getSlug()
            ];
        }

        if ($badge->getSerie() !== null) {
            $serie = [
                'id' => $badge->getSerie()->getId(),
                'name' => $badge->getSerie()->getName(),
                'slug' => $badge->getSerie()->getSlug()
            ];
        }

        if ($badge->getPlatform() !== null) {
            $platform = [
                'id' => $badge->getPlatform()->getId(),
                'name' => $badge->getPlatform()->getName(),
                'slug' => $badge->getPlatform()->getSlug()
            ];
        }

        return new BadgeDTO(
            id: $badge->getId() ?? 0,
            type: $badge->getType()->value,
            picture: $badge->getPicture() ?? '',
            value: $badge->getValue(),
            game: $game,
            serie: $serie,
            platform: $platform
        );
    }
}
