<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\User\Functional\Api;

class LoginTest extends AbstractFunctionalTestCase
{
    public function testLoginOK(): void
    {
        // Create a user for authentication
        $this->createAdminUser();

        $response = $this->apiClient->request('POST', '/api/login_check', ['json' => [
            'username' => 'admin',
            'password' => 'password', // Default password from createAdminUser
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
