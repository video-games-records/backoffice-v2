<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Dwh\Presentation\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Dwh\Presentation\Api\Controller\Player\GetPositions;
use App\BoundedContext\VideoGamesRecords\Dwh\Presentation\Api\Controller\Player\GetMedalsByTime;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/players/{id}/get-positions',
            controller: GetPositions::class,
            openapi: new Model\Operation(
                summary: 'Return player dwh stats positions',
                description: 'Return player dwh stats positions',
            ),
            read: false,
        ),
        new Get(
            uriTemplate: '/players/{id}/get-medals-by-time',
            controller: GetMedalsByTime::class,
            openapi: new Model\Operation(
                summary: 'Return player dwh stats medals',
                description: 'Return player dwh stats medals',
            ),
            read: false,
        )
    ],
)]

class Player
{
}
