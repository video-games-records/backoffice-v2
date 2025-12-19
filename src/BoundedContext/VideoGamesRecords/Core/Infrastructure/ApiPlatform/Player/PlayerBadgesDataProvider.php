<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerBadgesDTO;
use App\BoundedContext\VideoGamesRecords\Badge\Application\Mapper\PlayerBadgesMapper;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerRepository;

/** @implements ProviderInterface<PlayerBadgesDTO> */
class PlayerBadgesDataProvider implements ProviderInterface
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private PlayerBadgesMapper $playerBadgesMapper
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?PlayerBadgesDTO
    {
        $playerId = $uriVariables['id'] ?? null;

        if ($playerId === null) {
            return null;
        }

        $player = $this->playerRepository->find((int) $playerId);

        if ($player === null) {
            return null;
        }

        assert($player instanceof \App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player);

        return $this->playerBadgesMapper->toResponseDTO($player);
    }
}
