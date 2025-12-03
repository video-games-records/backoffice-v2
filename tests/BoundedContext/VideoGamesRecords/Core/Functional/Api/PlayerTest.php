<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\VideoGamesRecords\Core\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class PlayerTest extends ApiTestCase
{
    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/players');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Player',
            '@id' => '/api/players',
            '@type' => 'hydra:Collection',
        ]);

        $this->assertGreaterThan(0, count($response->toArray()['hydra:member']));
    }

    public function testGetItem(): void
    {
        $response = static::createClient()->request('GET', '/api/players/1');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Player',
            '@type' => 'Player',
            'id' => 1,
        ]);
    }

    public function testGetItemNotFound(): void
    {
        static::createClient()->request('GET', '/api/players/99999');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetRankingPointChart(): void
    {
        $response = static::createClient()->request('GET', '/api/players/ranking-point-chart');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Player',
            '@id' => '/api/players/ranking-point-chart',
            '@type' => 'hydra:Collection',
        ]);
    }

    public function testGetRankingPointGame(): void
    {
        $response = static::createClient()->request('GET', '/api/players/ranking-point-game');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Player',
            '@id' => '/api/players/ranking-point-game',
            '@type' => 'hydra:Collection',
        ]);
    }

    public function testGetRankingMedal(): void
    {
        $response = static::createClient()->request('GET', '/api/players/ranking-medal');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Player',
            '@id' => '/api/players/ranking-medal',
            '@type' => 'hydra:Collection',
        ]);
    }

    public function testGetRankingCup(): void
    {
        $response = static::createClient()->request('GET', '/api/players/ranking-cup');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Player',
            '@id' => '/api/players/ranking-cup',
            '@type' => 'hydra:Collection',
        ]);
    }

    public function testGetRankingBadge(): void
    {
        $response = static::createClient()->request('GET', '/api/players/ranking-badge');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Player',
            '@id' => '/api/players/ranking-badge',
            '@type' => 'hydra:Collection',
        ]);
    }

    public function testGetRankingProof(): void
    {
        $response = static::createClient()->request('GET', '/api/players/ranking-proof');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Player',
            '@id' => '/api/players/ranking-proof',
            '@type' => 'hydra:Collection',
        ]);
    }
}
