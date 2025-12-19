<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Country\Response;

class CountryDTO
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
