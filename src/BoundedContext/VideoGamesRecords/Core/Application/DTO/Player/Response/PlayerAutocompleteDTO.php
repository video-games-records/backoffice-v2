<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\PlayerAutocompleteDataProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/players/autocomplete',
            provider: PlayerAutocompleteDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Get players autocomplete',
                description: 'Retrieves simplified player data for autocomplete functionality'
            )
        )
    ]
)]
class PlayerAutocompleteDTO
{
    /**
     * @param \App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerBasicDTO[] $players
     */
    public function __construct(
        public readonly array $players
    ) {
    }
}
