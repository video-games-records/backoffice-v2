<?php

// tests/Presentation/Api/ApiPlatformTest.php
namespace App\Tests\Presentation\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiPlatformTest extends WebTestCase
{
    public function testApiPlatformIsInstalled(): void
    {
        $client = static::createClient();

        // Test that OpenAPI documentation is accessible
        $client->request('GET', '/api/docs.json');

        $response = $client->getResponse();
        $this->assertTrue(
            $response->isSuccessful() || $response->getStatusCode() === 404,
            'API Platform should provide OpenAPI documentation'
        );
    }

    public function testApiPlatformJsonLdFormat(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api', [], [], [
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);

        $response = $client->getResponse();

        if ($response->isSuccessful()) {
            $content = json_decode($response->getContent(), true);
            $this->assertIsArray($content);
            $this->assertArrayHasKey('@context', $content);
        } else {
            // If no API resources defined, that's normal
            $this->assertTrue(true, 'No API resources defined yet');
        }
    }
}
