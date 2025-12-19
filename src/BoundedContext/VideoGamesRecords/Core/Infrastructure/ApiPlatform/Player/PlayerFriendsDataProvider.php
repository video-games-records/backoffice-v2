<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerFriendsDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\Mapper\Player\PlayerFriendsMapper;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerRepository;

/** @implements ProviderInterface<PlayerFriendsDTO> */
class PlayerFriendsDataProvider implements ProviderInterface
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private PlayerFriendsMapper $playerFriendsMapper
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?PlayerFriendsDTO
    {
        $playerId = $uriVariables['id'] ?? null;

        if ($playerId === null) {
            return null;
        }

        $player = $this->playerRepository->find((int) $playerId);

        if ($player === null) {
            return null;
        }

        return $this->playerFriendsMapper->toResponseDTO($player);
    }
}
