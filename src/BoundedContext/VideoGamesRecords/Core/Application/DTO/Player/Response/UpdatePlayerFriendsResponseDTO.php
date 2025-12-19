<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\UpdatePlayerFriendsDataProvider;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/players/{id}/friends',
            input: \App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Request\UpdatePlayerFriendsRequestDTO::class,
            output: \App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\UpdatePlayerFriendsResponseDTO::class,
            processor: UpdatePlayerFriendsDataProvider::class,
            status: 200,
            security: 'is_granted("ROLE_USER")',
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Update player friends list',
                description: 'Add or remove friends from player\'s friends list'
            )
        )
    ]
)]
class UpdatePlayerFriendsResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly int $friendsAdded,
        public readonly int $friendsRemoved,
        public readonly int $totalFriends
    ) {
    }
}
