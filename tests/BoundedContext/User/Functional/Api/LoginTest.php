<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\User\Functional\Api;

use App\BoundedContext\User\Tests\Story\AdminUserStory;

class LoginTest extends AbstractFunctionalTestCase
{
    public function testLoginOK(): void
    {
        // Load the AdminUserStory to have consistent test users
        AdminUserStory::load();

        $response = $this->apiClient->request('POST', '/api/login_check', ['json' => [
            'username' => 'admin',
            'password' => 'admin',
        ]]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $arrayResponse = $response->toArray();
        $this->assertArrayHasKey('token', $arrayResponse);
        $this->assertNotEmpty($response->toArray()['token']);
    }

    public function testLoginNOK(): void
    {
        $response = $this->apiClient->request('POST', '/api/login_check', ['json' => [
            'username' => 'nobody@local.fr',
            'password' => 'nobody',
        ]]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonEquals([
            'code' => 401,
            'message' => 'Invalid credentials.',
        ]);
        $this->assertJsonContains([
            'message' => 'Invalid credentials.',
        ]);
    }
}
