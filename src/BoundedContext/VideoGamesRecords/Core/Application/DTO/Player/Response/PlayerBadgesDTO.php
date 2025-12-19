<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player\PlayerBadgesDataProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/players/{id}/badges',
            provider: PlayerBadgesDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Player'],
                summary: 'Get player badges',
                description: 'Retrieves all badges earned by a specific player grouped by type'
            )
        )
    ]
)]
class PlayerBadgesDTO
{
    /**
     * @param array<\App\BoundedContext\VideoGamesRecords\Badge\Application\DTO\Response\PlayerBadgeDTO> $special
     * @param array<\App\BoundedContext\VideoGamesRecords\Badge\Application\DTO\Response\PlayerBadgeDTO> $connexion
     * @param array<\App\BoundedContext\VideoGamesRecords\Badge\Application\DTO\Response\PlayerBadgeDTO> $forum
     * @param array<\App\BoundedContext\VideoGamesRecords\Badge\Application\DTO\Response\PlayerBadgeDTO> $don
     * @param array<\App\BoundedContext\VideoGamesRecords\Badge\Application\DTO\Response\PlayerBadgeDTO> $vgr_chart
     * @param array<\App\BoundedContext\VideoGamesRecords\Badge\Application\DTO\Response\PlayerBadgeDTO> $vgr_proof
     * @param array<\App\BoundedContext\VideoGamesRecords\Badge\Application\DTO\Response\PlayerBadgeDTO> $master
     * @param array<\App\BoundedContext\VideoGamesRecords\Badge\Application\DTO\Response\PlayerBadgeDTO> $platform
     * @param array<\App\BoundedContext\VideoGamesRecords\Badge\Application\DTO\Response\PlayerBadgeDTO> $serie
     */
    public function __construct(
        public readonly array $special,
        public readonly array $connexion,
        public readonly array $forum,
        public readonly array $don,
        public readonly array $vgr_chart,
        public readonly array $vgr_proof,
        public readonly array $master,
        public readonly array $platform,
        public readonly array $serie,
    ) {
    }
}
