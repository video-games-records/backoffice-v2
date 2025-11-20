<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\Message\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\Client;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BoundedContext\User\Tests\Factory\UserFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;
use Zenstruck\Foundry\Test\Factories;

class AbstractFunctionalTestCase extends ApiTestCase
{
    use Factories;

    protected Client $apiClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiClient = static::createClient();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function createAuthenticatedUser(array $roles = []): object
    {
        $user = UserFactory::new()
            ->withRoles($roles ?: ['ROLE_USER'])
            ->withCredentials('test@example.com', 'testuser', 'password')
            ->create();

        // Get JWT token via login
        $this->authenticateUser($user->_real());

        return $user;
    }

    protected function createAdminUser(): object
    {
        AdminUserStory::load();
        $adminUser = AdminUserStory::adminUser();

        $this->authenticateUser($adminUser->_real());

        return $adminUser;
    }

    protected function authenticateUser($user): void
    {
        // Login to get JWT token
        $loginResponse = $this->apiClient->request('POST', '/api/login_check', [
            'json' => [
                'username' => $user->getUsername(),
                'password' => 'password', // Default password from factory
            ]
        ]);

        if ($loginResponse->getStatusCode() !== 200) {
            // Try with known admin credentials if factory password fails
            $loginResponse = $this->apiClient->request('POST', '/api/login_check', [
                'json' => [
                    'username' => 'admin',
                    'password' => 'admin',
                ]
            ]);
        }

        $this->assertEquals(200, $loginResponse->getStatusCode());

        $token = $loginResponse->toArray()['token'];

        // Set Authorization header for subsequent requests
        $this->apiClient->setDefaultOptions([
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
        ]);
    }
}
