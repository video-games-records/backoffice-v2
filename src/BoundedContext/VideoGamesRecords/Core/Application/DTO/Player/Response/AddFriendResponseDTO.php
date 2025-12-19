<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\AddFriendDataProvider;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/players/add-friend',
            input: \App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Request\AddFriendRequestDTO::class,
            output: \App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\AddFriendResponseDTO::class,
            provider: AddFriendDataProvider::class,
            security: 'is_granted("ROLE_USER")',
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Add friend to player',
                description: 'Adds a friend to the current player\'s friend list'
            )
        )
    ]
)]
class AddFriendResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message
    ) {
    }
}
