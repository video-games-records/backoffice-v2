<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\PlayerNewLostPositionCountDataProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/players/{id}/get-nb-new-lost-position',
            provider: PlayerNewLostPositionCountDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Get new lost position count',
                description: 'Get new lost position count since last visit'
            )
        )
    ]
)]
class PlayerNewLostPositionCountDTO
{
    public function __construct(
        public readonly int $count,
    ) {
    }
}
