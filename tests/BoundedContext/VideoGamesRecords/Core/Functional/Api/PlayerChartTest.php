<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\VideoGamesRecords\Core\Functional\Api;

class PlayerChartTest extends AbstractFunctionalTestCase
{
    public function testGetCollection(): void
    {
        $response = $this->apiClient->request('GET', '/api/player_charts');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/PlayerChart',
            '@id' => '/api/player_charts',
            '@type' => 'hydra:Collection',
        ]);

        $responseData = $response->toArray();
        $this->assertArrayHasKey('hydra:member', $responseData);
        $this->assertIsArray($responseData['hydra:member']);
    }

    public function testGetItem(): void
    {
        // First get a collection to find an existing PlayerChart ID
        $collectionResponse = $this->apiClient->request('GET', '/api/player_charts', [
            'query' => ['itemsPerPage' => 1]
        ]);

        $this->assertResponseIsSuccessful();

        $collectionData = $collectionResponse->toArray();

        // Skip the test if no PlayerChart exists
        if (empty($collectionData['hydra:member'])) {
            $this->markTestSkipped('No PlayerChart found in database for testing GetItem');
        }

        $firstPlayerChart = $collectionData['hydra:member'][0];
        $playerChartId = $firstPlayerChart['id'];

        // Test getting the specific item
        $response = $this->apiClient->request('GET', "/api/player_charts/{$playerChartId}");

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/PlayerChart',
            '@type' => 'PlayerChart',
            'id' => $playerChartId,
        ]);

        $responseData = $response->toArray();
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('rank', $responseData);
        $this->assertArrayHasKey('pointChart', $responseData);
        $this->assertArrayHasKey('status', $responseData);
    }

    public function testGetItemNotFound(): void
    {
        $this->apiClient->request('GET', '/api/player_charts/99999');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetLatestScores(): void
    {
        $response = $this->apiClient->request('GET', '/api/player_charts/latest');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/PlayerChart',
            '@id' => '/api/player_charts/latest',
            '@type' => 'hydra:Collection',
        ]);

        $responseData = $response->toArray();
        $this->assertArrayHasKey('hydra:member', $responseData);
        $this->assertIsArray($responseData['hydra:member']);
    }


    public function testGetCollectionWithFilters(): void
    {
        // Test filtering by status
        $response = $this->apiClient->request('GET', '/api/player_charts', [
            'query' => ['status' => 'none']
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Test filtering by rank
        $response = $this->apiClient->request('GET', '/api/player_charts', [
            'query' => ['rank' => '1']
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testGetCollectionWithOrder(): void
    {
        // Test ordering by rank
        $response = $this->apiClient->request('GET', '/api/player_charts', [
            'query' => [
                'order[rank]' => 'ASC',
                'itemsPerPage' => 5
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Test ordering by pointChart
        $response = $this->apiClient->request('GET', '/api/player_charts', [
            'query' => [
                'order[pointChart]' => 'DESC',
                'itemsPerPage' => 5
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testGetLatestScoresWithParameters(): void
    {
        $response = $this->apiClient->request('GET', '/api/player_charts/latest', [
            'query' => [
                'days' => 30,
                'page' => 1,
                'itemsPerPage' => 10
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
