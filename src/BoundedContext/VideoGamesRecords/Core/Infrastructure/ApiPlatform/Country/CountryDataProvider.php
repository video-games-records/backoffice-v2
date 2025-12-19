<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Country;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Country\Response\CountryResourceDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\Mapper\CountryMapper;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\CountryRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CountryDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly CountryRepository $countryRepository,
        private readonly CountryMapper $countryMapper
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): CountryResourceDTO
    {
        $country = $this->countryRepository->find($uriVariables['id']);

        if (!$country) {
            throw new NotFoundHttpException('Country not found');
        }

        return $this->countryMapper->toCountryResourceDTO($country);
    }
}
