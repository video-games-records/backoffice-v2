<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerGameStatsDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\Mapper\PlayerGameStatsMapper;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerRepository;

/** @implements ProviderInterface<PlayerGameStatsDTO> */
class PlayerGameStatsDataProvider implements ProviderInterface
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private PlayerGameStatsMapper $playerGameStatsMapper
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?PlayerGameStatsDTO
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

        return $this->playerGameStatsMapper->toResponseDTO($player);
    }
}
