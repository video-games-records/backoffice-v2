<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Shared\Presentation\Api\Resources;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\VideoGamesRecords\Shared\Presentation\Api\Controller\GetStats;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/website/get-stats',
            controller: GetStats::class,
            openapi: new Model\Operation(
                summary: 'Return website stats',
                description: 'Return website stats',
            ),
            read: false
        )
    ],
)]

class Website
{
}
