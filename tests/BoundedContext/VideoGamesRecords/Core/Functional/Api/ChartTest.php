<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\VideoGamesRecords\Core\Functional\Api;

class ChartTest extends AbstractFunctionalTestCase
{
    public function testGetCollection(): void
    {
        $response = $this->apiClient->request('GET', '/api/charts');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:member', $data);
        $this->assertIsArray($data['hydra:member']);

        if (!empty($data['hydra:member'])) {
            $firstChart = $data['hydra:member'][0];
            $this->assertArrayHasKey('@id', $firstChart);
            $this->assertArrayHasKey('@type', $firstChart);
            $this->assertEquals('Chart', $firstChart['@type']);
            $this->assertArrayHasKey('id', $firstChart);
            $this->assertArrayHasKey('name', $firstChart);
            $this->assertArrayHasKey('slug', $firstChart);
        }
    }

    public function testGetItem(): void
    {
        $response = $this->apiClient->request('GET', '/api/charts/1');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();

        $this->assertArrayHasKey('@context', $data);
        $this->assertArrayHasKey('@id', $data);
        $this->assertArrayHasKey('@type', $data);
        $this->assertEquals('Chart', $data['@type']);

        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
        $this->assertEquals(1, $data['id']);

        $this->assertArrayHasKey('name', $data);
        $this->assertIsString($data['name']);

        $this->assertArrayHasKey('slug', $data);
        $this->assertIsString($data['slug']);

        // Test optional fields with proper null/value checks
        if (isset($data['isProofVideoOnly'])) {
            $this->assertIsBool($data['isProofVideoOnly']);
        }

        if (isset($data['isDlc'])) {
            $this->assertIsBool($data['isDlc']);
        }

        if (isset($data['nbPost'])) {
            $this->assertIsInt($data['nbPost']);
        }

        // Test relations
        if (isset($data['libs'])) {
            $this->assertIsArray($data['libs']);
        }
    }

    public function testGetItemNotFound(): void
    {
        $this->apiClient->request('GET', '/api/charts/999999');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetChartsByGroup(): void
    {
        $response = $this->apiClient->request('GET', '/api/groups/1/charts');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:member', $data);
        $this->assertIsArray($data['hydra:member']);

        // Group 1 (Mario Main Game) should have charts according to fixtures
        $this->assertNotEmpty($data['hydra:member'], 'Group 1 should have charts according to fixtures');

        $firstChart = $data['hydra:member'][0];
        $this->assertArrayHasKey('@id', $firstChart);
        $this->assertArrayHasKey('@type', $firstChart);
        $this->assertEquals('Chart', $firstChart['@type']);
        $this->assertArrayHasKey('id', $firstChart);
        $this->assertArrayHasKey('name', $firstChart);
        $this->assertArrayHasKey('slug', $firstChart);
    }

    public function testGetChartsByGroupNotFound(): void
    {
        $this->apiClient->request('GET', '/api/groups/999999/charts');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetChartPlayerRanking(): void
    {
        $response = $this->apiClient->request('GET', '/api/charts/1/player-ranking');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }

    public function testGetChartPlayerRankingPoints(): void
    {
        $response = $this->apiClient->request('GET', '/api/charts/1/player-ranking-points');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }

    public function testGetChartTeamRankingPoints(): void
    {
        $response = $this->apiClient->request('GET', '/api/charts/1/team-ranking-points');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }
}
