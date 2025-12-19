<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\VideoGamesRecords\Core\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BoundedContext\VideoGamesRecords\Core\Tests\Factory\CountryFactory;
use Zenstruck\Foundry\Test\Factories;

class CountryTest extends ApiTestCase
{
    use Factories;

    public function testGetCountriesCollection(): void
    {
        // Create some test countries
        CountryFactory::createMany(3);

        $response = static::createClient()->request('GET', '/api/countries');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/CountryResourceDTO',
            '@id' => '/api/countries',
            '@type' => 'hydra:Collection',
        ]);

        $this->assertGreaterThan(0, count($response->toArray()['hydra:member']));
    }

    public function testGetCountryItem(): void
    {
        // Create a test country
        $country = CountryFactory::createOne();

        $response = static::createClient()->request('GET', '/api/countries/' . $country->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/CountryResourceDTO',
            '@type' => 'CountryResourceDTO',
            'id' => $country->getId(),
        ]);

        $data = $response->toArray();
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('iso2', $data);
        $this->assertArrayHasKey('iso3', $data);
        $this->assertArrayHasKey('slug', $data);
    }

    public function testGetCountryItemNotFound(): void
    {
        static::createClient()->request('GET', '/api/countries/99999');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetCountriesCollectionWithSearch(): void
    {
        // Create test countries, one that should match the search
        CountryFactory::new()->france()->create();
        CountryFactory::createMany(2);

        $response = static::createClient()->request('GET', '/api/countries?search=FR');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/CountryResourceDTO',
            '@id' => '/api/countries',
            '@type' => 'hydra:Collection',
        ]);

        // Should return countries that match "FR" in name or ISO codes
        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:member', $data);
        $this->assertGreaterThan(0, count($data['hydra:member']));
    }
}
