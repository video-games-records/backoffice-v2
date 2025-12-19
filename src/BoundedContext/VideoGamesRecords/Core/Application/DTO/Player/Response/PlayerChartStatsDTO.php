<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\PlayerChartStatsDataProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/players/{id}/player-chart-stats',
            provider: PlayerChartStatsDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Get score stats',
                description: 'Get score stats'
            )
        )
    ]
)]
class PlayerChartStatsDTO
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public readonly string $status,
        public readonly int $nb,
    ) {
    }
}
