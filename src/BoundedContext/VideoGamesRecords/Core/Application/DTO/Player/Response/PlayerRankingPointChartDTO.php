<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\PlayerRankingPointChartDataProvider;
use App\BoundedContext\VideoGamesRecords\Team\Application\DTO\Response\TeamDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Country\Response\CountryDTO;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/players/ranking-point-chart',
            provider: PlayerRankingPointChartDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Get players ranking by chart points',
                description: 'Retrieves players ranking ordered by chart points'
            )
        )
    ]
)]
class PlayerRankingPointChartDTO
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public readonly int $id,
        public readonly string $pseudo,
        public readonly string $slug,
        public readonly int $rank,
        public readonly int $point,
        public readonly int $nbChart,
        public readonly ?TeamDTO $team,
        public readonly ?CountryDTO $country,
    ) {
    }
}
