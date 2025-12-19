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
            '@context' => '/api/contexts/PlayerResponseDTO',
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
            '@context' => '/api/contexts/PlayerResponseDTO',
            '@type' => 'PlayerResponseDTO',
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
            '@context' => '/api/contexts/PlayerRankingPointChartDTO',
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
            '@context' => '/api/contexts/PlayerRankingPointGameDTO',
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
            '@context' => '/api/contexts/PlayerRankingMedalDTO',
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
            '@context' => '/api/contexts/PlayerRankingCupDTO',
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
            '@context' => '/api/contexts/PlayerRankingBadgeDTO',
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
            '@context' => '/api/contexts/PlayerRankingProofDTO',
            '@id' => '/api/players/ranking-proof',
            '@type' => 'hydra:Collection',
        ]);
    }

    public function testUpdatePlayerProfileSuccess(): void
    {
        $client = static::createClient();

        // Authenticate with existing user
        $loginResponse = $client->request('POST', '/api/login_check', [
            'json' => [
                'username' => 'admin',
                'password' => 'admin',
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $token = $loginResponse->toArray()['token'];

        // Update player profile (using player ID 1)
        $response = $client->request('POST', '/api/players/1/profile', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'website' => 'https://example.com',
                'youtube' => 'https://youtube.com/channel/test',
                'twitch' => 'https://twitch.tv/testplayer',
                'discord' => 'testplayer#1234',
                'presentation' => 'I am a test player',
                'collection' => 'My test collection',
                'birthDate' => '1990-01-01',
                'countryId' => 1,
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            'success' => true,
            'message' => 'Profile updated successfully',
            'website' => 'https://example.com',
            'youtube' => 'https://youtube.com/channel/test',
            'twitch' => 'https://twitch.tv/testplayer',
            'discord' => 'testplayer#1234',
            'presentation' => 'I am a test player',
            'collection' => 'My test collection',
            'birthDate' => '1990-01-01',
        ]);
    }

    public function testUpdatePlayerProfilePartialUpdate(): void
    {
        $client = static::createClient();

        // Authenticate with existing user
        $loginResponse = $client->request('POST', '/api/login_check', [
            'json' => [
                'username' => 'admin',
                'password' => 'admin',
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $token = $loginResponse->toArray()['token'];

        // Update only presentation (using player ID 1)
        $response = $client->request('POST', '/api/players/1/profile', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'presentation' => 'Updated presentation only',
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'success' => true,
            'message' => 'Profile updated successfully',
            'presentation' => 'Updated presentation only',
        ]);
    }

    public function testUpdatePlayerProfileUnauthorized(): void
    {
        $client = static::createClient();

        $response = $client->request('POST', '/api/players/1/profile', [
            'json' => [
                'presentation' => 'Unauthorized update',
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdatePlayerProfileForbidden(): void
    {
        $client = static::createClient();

        // Authenticate with existing user
        $loginResponse = $client->request('POST', '/api/login_check', [
            'json' => [
                'username' => 'admin',
                'password' => 'admin',
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $token = $loginResponse->toArray()['token'];

        // Try to update another player's profile (using different player ID)
        $response = $client->request('POST', '/api/players/999/profile', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'presentation' => 'Trying to update another player',
            ]
        ]);

        // Either 403 forbidden or 404 not found depending on player existence
        $this->assertContains($response->getStatusCode(), [403, 404]);
    }

    public function testUpdatePlayerProfilePlayerNotFound(): void
    {
        $client = static::createClient();

        // Authenticate with existing user
        $loginResponse = $client->request('POST', '/api/login_check', [
            'json' => [
                'username' => 'admin',
                'password' => 'admin',
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $token = $loginResponse->toArray()['token'];

        // Try to update non-existent player
        $response = $client->request('POST', '/api/players/99999/profile', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'presentation' => 'Update non-existent player',
            ]
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testUpdatePlayerProfileValidationErrors(): void
    {
        $client = static::createClient();

        // Authenticate with existing user
        $loginResponse = $client->request('POST', '/api/login_check', [
            'json' => [
                'username' => 'admin',
                'password' => 'admin',
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $token = $loginResponse->toArray()['token'];

        // Test invalid URL
        $response = $client->request('POST', '/api/players/1/profile', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'website' => 'not-a-valid-url',
                'youtube' => 'invalid-youtube-url',
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testUpdatePlayerProfileMaxLengthValidation(): void
    {
        $client = static::createClient();

        // Authenticate with existing user
        $loginResponse = $client->request('POST', '/api/login_check', [
            'json' => [
                'username' => 'admin',
                'password' => 'admin',
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $token = $loginResponse->toArray()['token'];

        // Test max length validation
        $response = $client->request('POST', '/api/players/1/profile', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'discord' => str_repeat('a', 51), // Max 50 characters
                'presentation' => str_repeat('a', 1001), // Max 1000 characters
                'collection' => str_repeat('a', 501), // Max 500 characters
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testPlayerResponseIncludesCountryData(): void
    {
        // This test specifically checks that country data is embedded, not just referenced
        $response = static::createClient()->request('GET', '/api/players/1');

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();

        if (isset($data['country']) && $data['country'] !== null) {
            // If player has a country, it should include the country data, not just an IRI
            $this->assertArrayHasKey('name', $data['country']);
            $this->assertArrayHasKey('iso2', $data['country']);
            $this->assertArrayHasKey('iso3', $data['country']);
            $this->assertArrayHasKey('slug', $data['country']);

            // Should not be just an IRI reference like "/api/countries/1"
            $this->assertIsArray($data['country']);
            $this->assertNotIsString($data['country']);
        } else {
            // Country can be null, that's fine
            $this->assertNull($data['country']);
        }
    }
}
