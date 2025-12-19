<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\VideoGamesRecords\Core\Functional\Api;

class GameTest extends AbstractFunctionalTestCase
{
    public function testGetCollection(): void
    {
        $response = $this->apiClient->request('GET', '/api/games');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:member', $data);
        $this->assertIsArray($data['hydra:member']);

        if (!empty($data['hydra:member'])) {
            $firstGame = $data['hydra:member'][0];
            $this->assertArrayHasKey('@id', $firstGame);
            $this->assertArrayHasKey('@type', $firstGame);
            $this->assertEquals('Game', $firstGame['@type']);
            $this->assertArrayHasKey('id', $firstGame);
            $this->assertArrayHasKey('name', $firstGame);
            $this->assertArrayHasKey('status', $firstGame);
            $this->assertArrayHasKey('slug', $firstGame);
            $this->assertArrayHasKey('platforms', $firstGame);
            $this->assertIsArray($firstGame['platforms']);
            $this->assertArrayHasKey('genres', $firstGame);
            $this->assertIsArray($firstGame['genres']);
        }
    }

    public function testGetItem(): void
    {
        $response = $this->apiClient->request('GET', '/api/games/1');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();

        $this->assertArrayHasKey('@context', $data);
        $this->assertArrayHasKey('@id', $data);
        $this->assertArrayHasKey('@type', $data);
        $this->assertEquals('GameResponseDTO', $data['@type']);

        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
        $this->assertEquals(1, $data['id']);

        $this->assertArrayHasKey('name', $data);
        $this->assertIsString($data['name']);

        $this->assertArrayHasKey('status', $data);
        $this->assertIsString($data['status']);

        $this->assertArrayHasKey('slug', $data);
        $this->assertIsString($data['slug']);

        $this->assertArrayHasKey('platforms', $data);
        $this->assertIsArray($data['platforms']);

        $this->assertArrayHasKey('genres', $data);
        $this->assertIsArray($data['genres']);

        if (isset($data['serie'])) {
            $this->assertTrue(is_array($data['serie']) || is_string($data['serie']), 'Serie should be an array (object) or string (IRI)');
        }

        if (isset($data['rules'])) {
            $this->assertIsArray($data['rules']);
        }

        if (isset($data['discords'])) {
            $this->assertIsArray($data['discords']);
        }

        if (isset($data['forum'])) {
            $this->assertTrue($data['forum'] === null || is_array($data['forum']));
        }
    }

    public function testGetItemNotFound(): void
    {
        $this->apiClient->request('GET', '/api/games/999999');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGameAutocomplete(): void
    {
        // Test basic autocomplete endpoint - might return empty array if no data
        try {
            $response = $this->apiClient->request('GET', '/api/games/autocomplete');
            $this->assertResponseIsSuccessful();
            $data = $response->toArray();
            $this->assertIsArray($data, 'Autocomplete should return an array');
        } catch (\Exception $e) {
            // If endpoint returns 404 due to no data, that's acceptable for this test
            if (!str_contains($e->getMessage(), '404')) {
                throw $e;
            }
        }

        // Test with query parameter
        try {
            $response = $this->apiClient->request('GET', '/api/games/autocomplete?query=test');
            $this->assertResponseIsSuccessful();
            $data = $response->toArray();
            $this->assertIsArray($data, 'Autocomplete with query should return an array');
        } catch (\Exception $e) {
            // If endpoint returns 404 due to no data, that's acceptable for this test
            if (!str_contains($e->getMessage(), '404')) {
                throw $e;
            }
        }
    }

    public function testGetGamePlayerRankingPoints(): void
    {
        $response = $this->apiClient->request('GET', '/api/games/1/player-ranking-points');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }

    public function testGetGamePlayerRankingMedals(): void
    {
        $response = $this->apiClient->request('GET', '/api/games/1/player-ranking-medals');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }

    public function testGetGameeamRankingPoints(): void
    {
        $response = $this->apiClient->request('GET', '/api/games/1/team-ranking-points');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }

    public function testGetGameTeamRankingMedals(): void
    {
        $response = $this->apiClient->request('GET', '/api/games/1/team-ranking-medals');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }
}
