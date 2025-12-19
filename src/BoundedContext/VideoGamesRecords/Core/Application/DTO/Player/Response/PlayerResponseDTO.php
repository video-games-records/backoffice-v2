<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\PlayerDataProvider;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\PlayerCollectionDataProvider;
use App\BoundedContext\VideoGamesRecords\Team\Application\DTO\Response\TeamDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Country\Response\CountryDTO;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/players/{id}',
            provider: PlayerDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Get player details',
                description: 'Retrieves detailed information about a specific player including stats, social links, and team information'
            )
        ),
        new GetCollection(
            uriTemplate: '/players',
            provider: PlayerCollectionDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Get players list',
                description: 'Retrieves a paginated list of all players'
            )
        )
    ]
)]
class PlayerResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $pseudo,
        public readonly string $slug,
        public readonly int $nbConnexion,
        public readonly bool $hasDonate,
        public readonly PlayerStatusDTO $status,
        public readonly PlayerStatsDTO $stats,
        public readonly ?TeamDTO $team,
        public readonly ?\DateTimeInterface $lastLogin,
        public readonly \DateTimeInterface $createdAt,
        public readonly PlayerSocialLinksDTO $socialLinks,
        public readonly ?string $presentation,
        public readonly ?string $collection,
        public readonly ?CountryDTO $country,
        public readonly ?\DateTimeInterface $birthDate,
    ) {
    }
}
