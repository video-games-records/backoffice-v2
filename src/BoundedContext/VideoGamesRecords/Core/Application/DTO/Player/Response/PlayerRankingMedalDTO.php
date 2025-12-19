<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\PlayerRankingMedalDataProvider;
use App\BoundedContext\VideoGamesRecords\Team\Application\DTO\Response\TeamDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Country\Response\CountryDTO;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/players/ranking-medal',
            provider: PlayerRankingMedalDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Get players ranking by medals',
                description: 'Retrieves players ranking ordered by medals (platine, gold, silver, bronze)'
            )
        )
    ]
)]
class PlayerRankingMedalDTO
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public readonly int $id,
        public readonly string $pseudo,
        public readonly string $slug,
        public readonly int $rank,
        public readonly int $platine,
        public readonly int $gold,
        public readonly int $silver,
        public readonly int $bronze,
        public readonly ?TeamDTO $team,
        public readonly ?CountryDTO $country,
    ) {
    }
}
