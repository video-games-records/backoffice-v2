<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Country;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Country\Response\CountryResourceDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\Mapper\CountryMapper;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\CountryRepository;

/**
 * @implements ProviderInterface<CountryResourceDTO>
 */
class CountryCollectionDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly CountryRepository $countryRepository,
        private readonly CountryMapper $countryMapper
    ) {
    }

    /**
     * @return array<CountryResourceDTO>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $filters = $context['filters'] ?? [];
        $search = $filters['search'] ?? null;

        if (!empty($search)) {
            $countries = $this->countryRepository->findBySearchName($search);
        } else {
            $countries = $this->countryRepository->findAllOrderedByName();
        }

        return $this->countryMapper->toCountryResourceDTOCollection($countries);
    }
}
