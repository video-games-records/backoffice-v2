<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerGamesFromLostPositionsDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\Mapper\PlayerGamesFromLostPositionsMapper;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerRepository;

/** @implements ProviderInterface<PlayerGamesFromLostPositionsDTO> */
class PlayerGamesFromLostPositionsDataProvider implements ProviderInterface
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private PlayerGamesFromLostPositionsMapper $playerGamesFromLostPositionsMapper
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?PlayerGamesFromLostPositionsDTO
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

        return $this->playerGamesFromLostPositionsMapper->toResponseDTO($player);
    }
}
