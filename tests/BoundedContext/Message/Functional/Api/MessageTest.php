<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\Message\Functional\Api;

use App\BoundedContext\Message\Domain\Entity\Message;
use App\BoundedContext\Message\Tests\Factory\MessageFactory;
use App\BoundedContext\User\Tests\Factory\UserFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;

class MessageTest extends AbstractFunctionalTestCase
{
    public function testGetCollectionRequiresAuthentication(): void
    {
        $this->apiClient->request('GET', '/api/messages');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetCollection(): void
    {
        // Use simple approach - login with admin user
        AdminUserStory::load();

        // Login to get JWT token
        $loginResponse = $this->apiClient->request('POST', '/api/login_check', [
            'json' => [
                'username' => 'admin',
                'password' => 'admin',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $token = $loginResponse->toArray()['token'];

        // Make authenticated request
        $response = $this->apiClient->request('GET', '/api/messages', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Message::class);

        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:totalItems', $data);
    }

    public function testGetInboxMessages(): void
    {
        $user = $this->createAuthenticatedUser();
        $sender = UserFactory::new()->create();

        // Create inbox messages (received by user)
        MessageFactory::new()
            ->between($sender, $user)
            ->withContent('Test Inbox Message', 'This is a test message in inbox')
            ->unread()
            ->many(3)
            ->create();

        // Create outbox messages (sent by user) - should not appear in inbox
        MessageFactory::new()
            ->between($user, $sender)
            ->many(2)
            ->create();

        $response = $this->apiClient->request('GET', '/api/messages/inbox');

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();

        $this->assertGreaterThanOrEqual(3, $data['hydra:totalItems']);

        // Check that all messages in inbox are received by this user
        foreach ($data['hydra:member'] as $message) {
            $this->assertEquals($user->getId(), $message['recipient']['id']);
        }
    }

    public function testGetOutboxMessages(): void
    {
        $user = $this->createAuthenticatedUser();
        $recipient = UserFactory::new()->create();

        // Create outbox messages (sent by user)
        MessageFactory::new()
            ->between($user, $recipient)
            ->withContent('Test Outbox Message', 'This is a test message in outbox')
            ->many(3)
            ->create();

        // Create inbox messages (received by user) - should not appear in outbox
        MessageFactory::new()
            ->between($recipient, $user)
            ->many(2)
            ->create();

        $response = $this->apiClient->request('GET', '/api/messages/outbox');

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();

        $this->assertGreaterThanOrEqual(3, $data['hydra:totalItems']);

        // Check that all messages in outbox are sent by this user
        foreach ($data['hydra:member'] as $message) {
            $this->assertEquals($user->getId(), $message['sender']['id']);
        }
    }

    public function testGetItem(): void
    {
        $user = $this->createAuthenticatedUser();
        $otherUser = UserFactory::new()->create();

        $message = MessageFactory::new()
            ->between($user, $otherUser)
            ->withContent('Test Message', 'This is a test message')
            ->create();

        $response = $this->apiClient->request('GET', '/api/messages/' . $message->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Message::class);

        $data = $response->toArray();
        $this->assertEquals('Message', $data['@type']);
        $this->assertEquals($message->getId(), $data['id']);
        $this->assertEquals('Test Message', $data['object']);
        $this->assertEquals('This is a test message', $data['message']);
    }

    public function testGetItemAccessDeniedForUnauthorizedUser(): void
    {
        $user1 = $this->createAuthenticatedUser();
        $user2 = UserFactory::new()->create();
        $user3 = UserFactory::new()->create();

        // Create a message between user2 and user3 (user1 should not have access)
        $message = MessageFactory::new()
            ->between($user2, $user3)
            ->create();

        $this->apiClient->request('GET', '/api/messages/' . $message->getId());
        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateMessage(): void
    {
        $user = $this->createAuthenticatedUser();
        $recipient = UserFactory::new()->create();

        $messageData = [
            'object' => 'New Test Message',
            'message' => 'This is a new message created via API',
            'recipient' => '/api/users/' . $recipient->getId(),
        ];

        $response = $this->apiClient->request('POST', '/api/messages', [
            'json' => $messageData,
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertEquals('Message', $data['@type']);
        $this->assertEquals($messageData['object'], $data['object']);
        $this->assertEquals($messageData['message'], $data['message']);
        $this->assertEquals($user->getId(), $data['sender']['id']);
        $this->assertEquals($recipient->getId(), $data['recipient']['id']);
        $this->assertFalse($data['isOpened']);
    }

    public function testUpdateMessage(): void
    {
        $user = $this->createAuthenticatedUser();
        $otherUser = UserFactory::new()->create();

        $message = MessageFactory::new()
            ->between($otherUser, $user) // user is recipient
            ->unread()
            ->create();

        $updateData = [
            'isOpened' => true,
        ];

        $response = $this->apiClient->request('PUT', '/api/messages/' . $message->getId(), [
            'json' => $updateData,
        ]);

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertTrue($data['isOpened']);
    }

    public function testInboxMessagesWithFilters(): void
    {
        $user = $this->createAuthenticatedUser();
        $sender1 = UserFactory::new()->withCredentials('sender1@test.com', 'sender1')->create();
        $sender2 = UserFactory::new()->withCredentials('sender2@test.com', 'sender2')->create();

        // Create different types of messages
        MessageFactory::new()
            ->between($sender1, $user)
            ->urgent()
            ->unread()
            ->create();

        MessageFactory::new()
            ->between($sender2, $user)
            ->info()
            ->opened()
            ->create();

        MessageFactory::new()
            ->between($sender1, $user)
            ->ofType('DEFAULT')
            ->unread()
            ->create();

        // Test filter by type
        $response = $this->apiClient->request('GET', '/api/messages/inbox?type=URGENT');
        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertGreaterThanOrEqual(1, $data['hydra:totalItems']);

        // Test filter by sender
        $response = $this->apiClient->request('GET', '/api/messages/inbox?sender=' . $sender1->getId());
        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertGreaterThanOrEqual(2, $data['hydra:totalItems']);

        // Test filter by opened status
        $response = $this->apiClient->request('GET', '/api/messages/inbox?isOpened=false');
        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertGreaterThanOrEqual(2, $data['hydra:totalItems']);
    }

    public function testPagination(): void
    {
        $user = $this->createAuthenticatedUser();

        // Create more messages than the default pagination limit (10)
        MessageFactory::new()->toRecipient($user)->many(15)->create();

        $response = $this->apiClient->request('GET', '/api/messages/inbox?page=1');
        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertEquals(10, count($data['hydra:member']));
        $this->assertArrayHasKey('hydra:view', $data);
        $this->assertArrayHasKey('hydra:next', $data['hydra:view']);

        // Test second page
        $response = $this->apiClient->request('GET', '/api/messages/inbox?page=2');
        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertGreaterThanOrEqual(5, count($data['hydra:member']));
    }
}
