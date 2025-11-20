<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Dwh\Presentation\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use VideoGamesRecords\DwhBundle\Controller\Player\GetPositions;
use VideoGamesRecords\DwhBundle\Controller\Player\GetMedalsByTime;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/players/{id}/get-positions',
            controller: GetPositions::class,
            read: false,
            openapi: new Model\Operation(
                summary: 'Return player dwh stats positions',
                description: 'Return player dwh stats positions',
            ),
            /*openapiContext: [
            'parameters' => [
            [
            'name' => 'id',
            'in' => 'path',
            'type' => 'int'
            ]
            ]
            ]*/
        ),
        new Get(
            uriTemplate: '/players/{id}/get-medals-by-time',
            controller: GetMedalsByTime::class,
            read: false,
            openapi: new Model\Operation(
                summary: 'Return player dwh stats medals',
                description: 'Return player dwh stats medals',
            ),
            /*openapiContext: [
            'parameters' => [
            [
            'name' => 'id',
            'in' => 'path',
            'type' => 'int'
            ]
            ]
            ]*/
        )
    ],
)]

class Player
{
}
