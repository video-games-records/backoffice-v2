<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\Mapper;

use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Country;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Country\Response\CountryDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Country\Response\CountryResourceDTO;

class CountryMapper
{
    public function toCountryDTO(Country $country): CountryDTO
    {
        return new CountryDTO(
            id: $country->getId(),
            name: $country->getName() ?? $country->getDefaultName() ?? 'Unknown',
            iso2: $country->getCodeIso2(),
            iso3: $country->getCodeIso3(),
            slug: $country->getSlug()
        );
    }

    public function toCountryResourceDTO(Country $country): CountryResourceDTO
    {
        return new CountryResourceDTO(
            id: $country->getId(),
            name: $country->getName() ?? $country->getDefaultName() ?? 'Unknown',
            iso2: $country->getCodeIso2(),
            iso3: $country->getCodeIso3(),
            slug: $country->getSlug()
        );
    }

    /**
     * @param Country[] $countries
     * @return CountryDTO[]
     */
    public function toCountryDTOCollection(array $countries): array
    {
        return array_map(
            fn(Country $country) => $this->toCountryDTO($country),
            $countries
        );
    }

    /**
     * @param Country[] $countries
     * @return CountryResourceDTO[]
     */
    public function toCountryResourceDTOCollection(array $countries): array
    {
        return array_map(
            fn(Country $country) => $this->toCountryResourceDTO($country),
            $countries
        );
    }
}
