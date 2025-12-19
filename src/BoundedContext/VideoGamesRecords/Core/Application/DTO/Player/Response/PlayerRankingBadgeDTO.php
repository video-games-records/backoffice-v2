<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\PlayerRankingBadgeDataProvider;
use App\BoundedContext\VideoGamesRecords\Team\Application\DTO\Response\TeamDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Country\Response\CountryDTO;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/players/ranking-badge',
            provider: PlayerRankingBadgeDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Get players ranking by point badge',
                description: 'Retrieves players ranking ordered by point badge.',
            )
        )
    ]
)]
class PlayerRankingBadgeDTO
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public readonly int $id,
        public readonly string $pseudo,
        public readonly string $slug,
        public readonly int $rank,
        public readonly int $point,
        public readonly int $nbMasterBadge,
        public readonly ?TeamDTO $team,
        public readonly ?CountryDTO $country,
    ) {
    }
}
