<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\Message\Functional\Api;

class MessageApiHelperTest extends AbstractFunctionalTestCase
{
    public function testGetNbNewMessage(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->apiClient->request('GET', '/api/messages/get-nb-new-message');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = $response->toArray();
        $this->assertArrayHasKey('count', $data);
    }

    public function testGetRecipients(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->apiClient->request('GET', '/api/messages/get-recipients');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }

    public function testGetSenders(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->apiClient->request('GET', '/api/messages/get-senders');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = $response->toArray();
        $this->assertIsArray($data);
    }

    public function testHelperEndpointsRequireAuthentication(): void
    {
        $unauthenticatedClient = static::createClient();

        $unauthenticatedClient->request('GET', '/api/messages/get-nb-new-message');
        $this->assertResponseStatusCodeSame(401);

        $unauthenticatedClient->request('GET', '/api/messages/get-recipients');
        $this->assertResponseStatusCodeSame(401);

        $unauthenticatedClient->request('GET', '/api/messages/get-senders');
        $this->assertResponseStatusCodeSame(401);
    }
}
