<?php

// tests/Presentation/Web/ApplicationAvailabilityTest.php
namespace App\Tests\Presentation\Web;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityTest extends WebTestCase
{
    /**
     * Test that the application starts correctly
     */
    public function testApplicationStarts(): void
    {
        $client = static::createClient();

        // Basic test that the container loads
        $this->assertNotNull($client->getContainer());
    }

    /**
     * Test basic API routes (if they exist)
     */
    public function testApiDocumentationIsAccessible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api');

        // API Platform should return 200 or redirect to documentation
        $this->assertTrue(
            $client->getResponse()->isSuccessful() ||
            $client->getResponse()->isRedirection()
        );
    }

    /**
     * Test that the API login route exists
     */
    public function testApiLoginRouteExists(): void
    {
        $client = static::createClient();

        // Test POST to /api/login_check without credentials (should return 400 or 401)
        $client->request('POST', '/api/login_check', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], '{}');

        $response = $client->getResponse();

        // The route must exist (not 404)
        $this->assertNotEquals(404, $response->getStatusCode());
        // Without credentials, we expect 400 or 401
        $this->assertTrue(
            in_array($response->getStatusCode(), [400, 401])
        );
    }
}
