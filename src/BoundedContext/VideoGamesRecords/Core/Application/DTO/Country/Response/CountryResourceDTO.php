<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Country\Response;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/countries',
            provider: \App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Country\CountryCollectionDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Country'],
                summary: 'Get list of countries',
                description: 'Retrieves a collection of all countries'
            )
        ),
        new Get(
            uriTemplate: '/countries/{id}',
            provider: \App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Country\CountryDataProvider::class,
            openapi: new Model\Operation(
                tags: ['Country'],
                summary: 'Get a country',
                description: 'Retrieves a specific country by ID'
            )
        )
    ],
    paginationEnabled: false
)]
class CountryResourceDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $iso2,
        public readonly string $iso3,
        public readonly string $slug
    ) {
    }
}
