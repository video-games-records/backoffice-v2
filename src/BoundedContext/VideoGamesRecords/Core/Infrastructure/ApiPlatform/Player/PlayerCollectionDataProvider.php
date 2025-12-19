<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerResponseDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\Mapper\PlayerMapper;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerRepository;

/**
 * @implements ProviderInterface<PlayerResponseDTO>
 */
class PlayerCollectionDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly PlayerMapper $playerMapper
    ) {
    }

    /**
     * @return array<PlayerResponseDTO>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $filters = $context['filters'] ?? [];
        $limit = isset($filters['limit']) ? (int) $filters['limit'] : 30;
        $offset = isset($filters['offset']) ? (int) $filters['offset'] : 0;
        $search = $filters['search'] ?? null;

        // Si il y a une recherche, on utilise une requête personnalisée
        if (!empty($search)) {
            $players = $this->playerRepository->findBySearch($search, $limit, $offset);
        } else {
            $players = $this->playerRepository->findBy(
                criteria: [],
                orderBy: ['pseudo' => 'ASC'],
                limit: $limit,
                offset: $offset
            );
        }

        return array_map(
            fn($player) => $this->playerMapper->toPlayerResponseDTO($player),
            $players
        );
    }
}
