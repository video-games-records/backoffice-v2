<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\PlayerGameStatsDataProvider;

#[ApiResource(
    uriTemplate: '/players/{id}/game-stats',
    operations: [
        new Get(
            provider: PlayerGameStatsDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Get player game statistics',
                description: 'Retrieves detailed statistics for a player across all games'
            )
        )
    ]
)]
class PlayerGameStatsDTO
{
    /**
     * @param PlayerGameStatItemDTO[] $playerGames
     */
    public function __construct(
        public readonly array $playerGames
    ) {
    }
}
