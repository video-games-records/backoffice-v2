<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\UpdatePlayerProfilePostProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/players/{id}/profile',
            input: \App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Request\UpdatePlayerProfileRequestDTO::class,
            output: \App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\UpdatePlayerProfilePostResponseDTO::class,
            processor: UpdatePlayerProfilePostProcessor::class,
            status: 200,
            security: 'is_granted("ROLE_USER")',
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Update player profile',
                description: 'Updates player profile information'
            )
        )
    ]
)]
class UpdatePlayerProfilePostResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly ?string $website = null,
        public readonly ?string $youtube = null,
        public readonly ?string $twitch = null,
        public readonly ?string $discord = null,
        public readonly ?string $presentation = null,
        public readonly ?string $collection = null,
        public readonly ?string $birthDate = null,
        public readonly ?int $countryId = null
    ) {
    }
}
