<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\User\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\Client;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BoundedContext\User\Tests\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;

class AbstractFunctionalTestCase extends ApiTestCase
{
    use Factories;

    protected Client $apiClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiClient = static::createClient();

        // DAMA handles database transactions automatically
        // No manual schema creation needed
    }

    /**
     * Helper method pour créer un utilisateur admin quand nécessaire
     */
    protected function createAdminUser(): object
    {
        $unique = uniqid();
        return UserFactory::new()
            ->asSuperAdmin()
            ->withCredentials("admin{$unique}@test.com", "admin{$unique}", 'password')
            ->create();
    }

    /**
     * Helper method pour créer un utilisateur normal quand nécessaire
     */
    protected function createUser(array $overrides = []): object
    {
        $unique = uniqid();
        return UserFactory::new()
            ->withCredentials(
                $overrides['email'] ?? "user{$unique}@test.com",
                $overrides['username'] ?? "testuser{$unique}",
                $overrides['password'] ?? 'password'
            )
            ->create();
    }

    /**
     * Helper method pour authentifier un utilisateur via JWT
     */
    protected function authenticateUser(object $user): void
    {
        $realUser = $user->_real();

        $loginResponse = $this->apiClient->request('POST', '/api/login_check', [
            'json' => [
                'username' => $realUser->getUsername(),
                'password' => 'password', // Password from factory
            ]
        ]);

        $this->assertEquals(200, $loginResponse->getStatusCode());
        $token = $loginResponse->toArray()['token'];

        $this->apiClient->setDefaultOptions([
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
        ]);
    }
}
