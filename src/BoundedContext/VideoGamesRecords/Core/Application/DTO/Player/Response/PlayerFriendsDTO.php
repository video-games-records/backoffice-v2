<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\PlayerFriendsDataProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/players/{id}/friends',
            provider: PlayerFriendsDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Get player friends',
                description: 'Get player friends'
            )
        )
    ]
)]
class PlayerFriendsDTO
{
    /**
     * @param PlayerBasicDTO[] $friends
     */
    public function __construct(
        public readonly array $friends
    ) {
    }
}
