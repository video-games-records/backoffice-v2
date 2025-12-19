<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\PlayerGamesFromLostPositionsDataProvider;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Game\Response\GameBasicDTO;

#[ApiResource(
    uriTemplate: '/players/{id}/games-from-lost-positions',
    operations: [
        new Get(
            provider: PlayerGamesFromLostPositionsDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Get games with lost positions',
                description: 'Retrieves games where the player has lost ranking positions'
            )
        )
    ]
)]
class PlayerGamesFromLostPositionsDTO
{
    /**
     * @param GameBasicDTO[] $games
     */
    public function __construct(
        public readonly array $games
    ) {
    }
}
