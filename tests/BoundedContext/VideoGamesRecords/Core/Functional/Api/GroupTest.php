<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\VideoGamesRecords\Core\Functional\Api;

use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Group;

class GroupTest extends AbstractFunctionalTestCase
{
    public function testGetItem(): void
    {
        $response = $this->apiClient->request('GET', '/api/groups/1');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();

        $this->assertArrayHasKey('@context', $data);
        $this->assertArrayHasKey('@id', $data);
        $this->assertArrayHasKey('@type', $data);
        $this->assertEquals('Group', $data['@type']);

        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
        $this->assertEquals(1, $data['id']);

        $this->assertArrayHasKey('name', $data);
        $this->assertIsString($data['name']);

        $this->assertArrayHasKey('slug', $data);
        $this->assertIsString($data['slug']);

        // Test optional fields with proper null/value checks
        if (isset($data['isDlc'])) {
            $this->assertIsBool($data['isDlc']);
        }

        if (isset($data['isRank'])) {
            $this->assertIsBool($data['isRank']);
        }

        if (isset($data['nbChart'])) {
            $this->assertIsInt($data['nbChart']);
        }

        if (isset($data['nbPost'])) {
            $this->assertIsInt($data['nbPost']);
        }

        if (isset($data['nbPlayer'])) {
            $this->assertIsInt($data['nbPlayer']);
        }
    }

    public function testGetItemNotFound(): void
    {
        $this->apiClient->request('GET', '/api/groups/999999');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetGroupsByGame(): void
    {
        $response = $this->apiClient->request('GET', '/api/games/1/groups');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:member', $data);
        $this->assertIsArray($data['hydra:member']);

        // Check if pagination is enabled or disabled
        if (array_key_exists('hydra:totalItems', $data)) {
            $this->assertIsInt($data['hydra:totalItems']);
        }

        if (!empty($data['hydra:member'])) {
            $firstGroup = $data['hydra:member'][0];
            $this->assertArrayHasKey('@id', $firstGroup);
            $this->assertArrayHasKey('@type', $firstGroup);
            $this->assertEquals('Group', $firstGroup['@type']);
            $this->assertArrayHasKey('id', $firstGroup);
            $this->assertArrayHasKey('name', $firstGroup);
            $this->assertArrayHasKey('slug', $firstGroup);
        }
    }


    public function testGetGroupTopScore(): void
    {
        $response = $this->apiClient->request('GET', '/api/groups/1/top-score');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }

    public function testGetGroupPlayerRankingPoints(): void
    {
        $response = $this->apiClient->request('GET', '/api/groups/1/player-ranking-points');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }

    public function testGetGroupPlayerRankingMedals(): void
    {
        $response = $this->apiClient->request('GET', '/api/groups/1/player-ranking-medals');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }

    public function testGetGroupTeamRankingPoints(): void
    {
        $response = $this->apiClient->request('GET', '/api/groups/1/team-ranking-points');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }

    public function testGetGroupTeamRankingMedals(): void
    {
        $response = $this->apiClient->request('GET', '/api/groups/1/team-ranking-medals');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }
}
