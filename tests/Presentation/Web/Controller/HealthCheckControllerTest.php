<?php

declare(strict_types=1);

namespace App\Tests\Presentation\Web\Controller;

use App\BoundedContext\User\Tests\Factory\UserFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * Test suite for health check endpoints
 */
class HealthCheckControllerTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    public function testBasicHealthCheck(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('timestamp', $response);
        $this->assertArrayHasKey('environment', $response);
        $this->assertArrayHasKey('version', $response);

        $this->assertEquals('ok', $response['status']);
        $this->assertEquals('test', $response['environment']);
    }

    public function testDetailedHealthCheck(): void
    {
        $client = static::createClient();
        $this->loginAsAdmin($client);

        $client->request('GET', '/health/detailed');

        $this->assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('checks', $response);

        // Check that all expected health checks are present
        $expectedChecks = ['database', 'cache', 'disk_space', 'log_directory', 'memory'];
        foreach ($expectedChecks as $checkName) {
            $this->assertArrayHasKey($checkName, $response['checks']);
            $this->assertArrayHasKey('status', $response['checks'][$checkName]);
            $this->assertArrayHasKey('message', $response['checks'][$checkName]);
        }
    }

    public function testReadinessProbe(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health/ready');

        $this->assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('checks', $response);
        $this->assertEquals('ready', $response['status']);
    }

    public function testLivenessProbe(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health/live');

        $this->assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('timestamp', $response);
        $this->assertEquals('alive', $response['status']);
    }

    public function testHealthCheckResponseFormat(): void
    {
        $client = static::createClient();
        $this->loginAsAdmin($client);

        $client->request('GET', '/health/detailed');

        $response = json_decode($client->getResponse()->getContent(), true);

        // Validate response structure
        $this->assertIsString($response['status']);
        $this->assertIsInt($response['timestamp']);
        $this->assertIsString($response['environment']);
        $this->assertIsString($response['version']);
        $this->assertIsArray($response['checks']);

        // Validate each check has required fields
        foreach ($response['checks'] as $checkName => $check) {
            $this->assertIsString($check['status']);
            $this->assertIsString($check['message']);
            $this->assertContains($check['status'], ['healthy', 'warning', 'error']);
        }
    }

    public function testHealthCheckAuthentication(): void
    {
        // Health checks should be accessible without authentication
        $client = static::createClient();

        $publicEndpoints = ['/health', '/health/live', '/health/ready'];

        foreach ($publicEndpoints as $endpoint) {
            $client->request('GET', $endpoint);
            $this->assertResponseIsSuccessful(sprintf('Endpoint %s should be accessible', $endpoint));
        }
    }

    public function testMetricsEndpointRequiresSuperAdmin(): void
    {
        $client = static::createClient();
        $this->loginAsAdmin($client);

        $client->request('GET', '/health/metrics');

        $this->assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('metrics', $response);
        $this->assertArrayHasKey('timestamp', $response);
        $this->assertArrayHasKey('user', $response);

        // Check metrics structure
        $metrics = $response['metrics'];
        $expectedMetrics = ['memory', 'disk', 'php', 'system'];
        foreach ($expectedMetrics as $metricName) {
            $this->assertArrayHasKey($metricName, $metrics);
        }
    }

    public function testDetailedHealthCheckWithOnTheFlyUser(): void
    {
        $client = static::createClient();

        // Create an admin user on the fly using UserFactory
        $adminUser = UserFactory::new()
            ->asSuperAdmin()
            ->withCredentials('test-admin@example.com', 'test-admin')
            ->create();

        $client->loginUser($adminUser->_real());

        $client->request('GET', '/health/detailed');
        $this->assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('user', $response);
        $this->assertEquals('test-admin@example.com', $response['user']);
    }

    /**
     * Helper method to login admin user with session authentication using Factory
     */
    private function loginAsAdmin(KernelBrowser $client): void
    {
        // Load the admin user story to ensure consistent test data
        AdminUserStory::load();

        // Get the admin user from the story
        $adminUser = AdminUserStory::adminUser();

        // Simulate login by setting the user in the session
        $client->loginUser($adminUser->_real());
    }
}
