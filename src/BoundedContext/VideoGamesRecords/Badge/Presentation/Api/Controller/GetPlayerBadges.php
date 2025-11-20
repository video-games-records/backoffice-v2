<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Badge\Presentation\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\ValueObject\BadgeType;
use App\BoundedContext\VideoGamesRecords\Badge\Infrastructure\Doctrine\Repository\PlayerBadgeRepository;

class GetPlayerBadges extends AbstractController
{
    public function __construct(
        private readonly PlayerBadgeRepository $playerBadgeRepository
    ) {
    }

    /**
     * @return array<string, array<\App\BoundedContext\VideoGamesRecords\Badge\Domain\Entity\PlayerBadge>>
     */
    public function __invoke(Player $player): array
    {
        $result = [];

        $result['special'] = $this->playerBadgeRepository->findByPlayerAndType(
            $player,
            BadgeType::getSpecialBadgeValues(),
            ['pb.createdAt' => 'ASC']
        );

        $result['connexion'] = $this->playerBadgeRepository->findByPlayerAndType(
            $player,
            BadgeType::CONNEXION->value,
            ['b.value' => 'ASC']
        );

        $result['forum'] = $this->playerBadgeRepository->findByPlayerAndType(
            $player,
            BadgeType::FORUM->value,
            ['b.value' => 'ASC']
        );

        $result['don'] = $this->playerBadgeRepository->findByPlayerAndType(
            $player,
            BadgeType::DON->value,
            ['b.value' => 'ASC']
        );

        $result['vgr_chart'] = $this->playerBadgeRepository->findByPlayerAndType(
            $player,
            BadgeType::VGR_CHART->value,
            ['b.value' => 'ASC']
        );

        $result['vgr_proof'] = $this->playerBadgeRepository->findByPlayerAndType(
            $player,
            BadgeType::VGR_PROOF->value,
            ['b.value' => 'ASC']
        );

        $result['master'] = $this->playerBadgeRepository->findByPlayerAndType(
            $player,
            BadgeType::MASTER->value,
            ['pb.mbOrder' => 'ASC']
        );

        $result['platform'] = $this->playerBadgeRepository->findByPlayerAndType(
            $player,
            BadgeType::PLATFORM->value,
            ['pb.createdAt' => 'ASC']
        );

        $result['serie'] = $this->playerBadgeRepository->findByPlayerAndType(
            $player,
            BadgeType::SERIE->value,
            ['pb.createdAt' => 'ASC']
        );

        return $result;
    }
}
