<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\Forum\Functional\Api;

use App\BoundedContext\Forum\Domain\Entity\Message;
use App\BoundedContext\Forum\Tests\Factory\CategoryFactory;
use App\BoundedContext\Forum\Tests\Factory\ForumFactory;
use App\BoundedContext\Forum\Tests\Factory\ForumMessageFactory;
use App\BoundedContext\Forum\Tests\Factory\TopicFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;

class MessageTest extends AbstractFunctionalTestCase
{
    public function testGetCollection(): void
    {
        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $topic = TopicFactory::new()
            ->inForum($forum)
            ->create();

        ForumMessageFactory::new()
            ->inTopic($topic)
            ->many(5)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_messages');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Message::class);

        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:totalItems', $data);
        $this->assertEquals(5, $data['hydra:totalItems']);
    }

    public function testGetSingleMessage(): void
    {
        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $topic = TopicFactory::new()
            ->inForum($forum)
            ->create();

        $message = ForumMessageFactory::new()
            ->inTopic($topic)
            ->withContent('Test message content')
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_messages/' . $message->getId());

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Message::class);

        $data = $response->toArray();
        $this->assertEquals($message->getId(), $data['id']);
        $this->assertEquals('Test message content', $data['message']);
    }

    public function testGetMessagesByTopic(): void
    {
        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $topic1 = TopicFactory::new()
            ->inForum($forum)
            ->create();

        $topic2 = TopicFactory::new()
            ->inForum($forum)
            ->create();

        // Create messages for topic1
        ForumMessageFactory::new()
            ->inTopic($topic1)
            ->many(3)
            ->create();

        // Create messages for topic2
        ForumMessageFactory::new()
            ->inTopic($topic2)
            ->many(2)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topics/' . $topic1->getId() . '/messages');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:totalItems', $data);
        $this->assertEquals(3, $data['hydra:totalItems']);
    }

    public function testCreateMessageRequiresAuthentication(): void
    {
        // Use fixtures - avoid creating new entities that need users
        // Just test the API endpoint
        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        // Create a simple topic with explicit user to avoid factory issues
        AdminUserStory::load();
        $user = AdminUserStory::adminUser();

        $topic = TopicFactory::new()
            ->inForum($forum)
            ->byUser($user->_real())
            ->create();

        $this->apiClient->request('POST', '/api/forum_messages', [
            'json' => [
                'message' => 'New message content',
                'topic' => '/api/forum_topics/' . $topic->getId(),
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateMessage(): void
    {
        AdminUserStory::load();
        $this->createAdminUser();

        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $topic = TopicFactory::new()
            ->inForum($forum)
            ->create();

        $response = $this->apiClient->request('POST', '/api/forum_messages', [
            'json' => [
                'message' => 'This is a new message in the topic',
                'topic' => '/api/forum_topics/' . $topic->getId(),
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertEquals('This is a new message in the topic', $data['message']);
    }

    public function testUpdateMessageRequiresOwnership(): void
    {
        AdminUserStory::load();
        $adminUser = AdminUserStory::adminUser();

        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $topic = TopicFactory::new()
            ->inForum($forum)
            ->create();

        $message = ForumMessageFactory::new()
            ->inTopic($topic)
            ->byUser($adminUser)
            ->create();

        // Try to update without authentication
        $this->apiClient->request('PUT', '/api/forum_messages/' . $message->getId(), [
            'json' => [
                'message' => 'Updated message content',
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);

        // Authenticate as different user
        $otherUser = $this->createAuthenticatedUser();

        $this->apiClient->request('PUT', '/api/forum_messages/' . $message->getId(), [
            'json' => [
                'message' => 'Updated message content',
            ]
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUpdateOwnMessage(): void
    {
        AdminUserStory::load();
        $adminUser = $this->createAdminUser();

        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $topic = TopicFactory::new()
            ->inForum($forum)
            ->create();

        $message = ForumMessageFactory::new()
            ->inTopic($topic)
            ->byUser($adminUser)
            ->withContent('Original message')
            ->create();

        $response = $this->apiClient->request('PUT', '/api/forum_messages/' . $message->getId(), [
            'json' => [
                'message' => 'Updated message content',
            ]
        ]);

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertEquals('Updated message content', $data['message']);
    }

    public function testMessagesOrderedById(): void
    {
        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $topic = TopicFactory::new()
            ->inForum($forum)
            ->create();

        ForumMessageFactory::new()
            ->inTopic($topic)
            ->many(5)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topics/' . $topic->getId() . '/messages');

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $messages = $data['hydra:member'];

        // Check that messages are ordered by ID (ASC by default)
        $this->assertGreaterThan(0, count($messages));

        $ids = array_column($messages, 'id');
        $sortedIds = $ids;
        sort($sortedIds);
        $this->assertEquals($sortedIds, $ids);
    }

    public function testMessageWithUserRelation(): void
    {
        AdminUserStory::load();
        $adminUser = AdminUserStory::adminUser();

        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $topic = TopicFactory::new()
            ->inForum($forum)
            ->create();

        $message = ForumMessageFactory::new()
            ->inTopic($topic)
            ->byUser($adminUser)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_messages/' . $message->getId());

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertArrayHasKey('user', $data);
        $this->assertEquals('admin', $data['user']['username']);
    }

    public function testFilterMessagesByUser(): void
    {
        AdminUserStory::load();
        $adminUser = AdminUserStory::adminUser();
        $regularUser = AdminUserStory::regularUser();

        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $topic = TopicFactory::new()
            ->inForum($forum)
            ->create();

        // Create messages by admin user
        ForumMessageFactory::new()
            ->inTopic($topic)
            ->byUser($adminUser)
            ->many(2)
            ->create();

        // Create messages by regular user
        ForumMessageFactory::new()
            ->inTopic($topic)
            ->byUser($regularUser)
            ->many(3)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_messages?user=' . $adminUser->getId());

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertEquals(2, $data['hydra:totalItems']);
    }
}
