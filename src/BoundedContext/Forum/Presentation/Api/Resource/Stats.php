<?php

declare(strict_types=1);

namespace App\BoundedContext\Forum\Presentation\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\BoundedContext\Forum\Presentation\Api\Controller\GetStats;

#[ApiResource(
    shortName: 'ForumStats',
    operations: [
        new Get(
            uriTemplate: '/forum_stats',
            controller: GetStats::class,
            read: false,
        )
    ],
)]
class Stats
{
}
