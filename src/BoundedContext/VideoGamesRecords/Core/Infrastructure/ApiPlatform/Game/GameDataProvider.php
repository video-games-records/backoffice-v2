<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Game;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Game\Response\GameResponseDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\Mapper\GameMapper;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Repository\GameRepositoryInterface;

class GameDataProvider implements ProviderInterface
{
    public function __construct(
        private GameRepositoryInterface $gameRepository,
        private GameMapper $gameMapper
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?GameResponseDTO
    {
        $id = $uriVariables['id'] ?? null;

        if ($id === null) {
            return null;
        }

        $game = $this->gameRepository->findById((int) $id);

        if ($game === null) {
            return null;
        }

        return $this->gameMapper->toResponseDTO($game);
    }
}
