<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Team\Application\DTO\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Team\Infrastructure\ApiPlatform\TeamPlayersDataProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/teams/{id}/players',
            provider: TeamPlayersDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Team'],
                summary: 'Get players team',
                description: 'Retrieves players from team',
            )
        )
    ]
)]
class TeamPlayersDTO
{
    /**
     * @param \App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerBasicDTO[] $players
     */
    public function __construct(
        public readonly array $players
    ) {
    }
}
