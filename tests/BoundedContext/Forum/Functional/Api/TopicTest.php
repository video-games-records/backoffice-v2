<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\Forum\Functional\Api;

use App\BoundedContext\Forum\Domain\Entity\Topic;
use App\BoundedContext\Forum\Tests\Factory\CategoryFactory;
use App\BoundedContext\Forum\Tests\Factory\ForumFactory;
use App\BoundedContext\Forum\Tests\Factory\TopicFactory;
use App\BoundedContext\Forum\Tests\Factory\TopicTypeFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;

class TopicTest extends AbstractFunctionalTestCase
{
    public function testGetCollection(): void
    {
        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        TopicFactory::new()
            ->inForum($forum)
            ->many(3)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topics');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Topic::class);

        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:totalItems', $data);
        $this->assertEquals(3, $data['hydra:totalItems']);
    }

    public function testGetSingleTopic(): void
    {
        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $topic = TopicFactory::new()
            ->discussion()
            ->inForum($forum)
            ->withContent('Test Topic')
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topics/' . $topic->getId());

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Topic::class);

        $data = $response->toArray();
        $this->assertEquals($topic->getId(), $data['id']);
        $this->assertEquals('Test Topic', $data['name']);
    }

    public function testCreateTopicRequiresAuthentication(): void
    {
        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $topicType = TopicTypeFactory::new()
            ->discussion()
            ->create();

        $this->apiClient->request('POST', '/api/forum_topics', [
            'json' => [
                'name' => 'New Topic',
                'forum' => '/api/forum_forums/' . $forum->getId(),
                'type' => '/api/forum_topic_types/' . $topicType->getId(),
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateTopic(): void
    {
        AdminUserStory::load();
        $this->createAdminUser();

        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $topicType = TopicTypeFactory::new()
            ->discussion()
            ->create();

        $response = $this->apiClient->request('POST', '/api/forum_topics', [
            'json' => [
                'name' => 'New Discussion Topic',
                'forum' => '/api/forum_forums/' . $forum->getId(),
                'type' => '/api/forum_topic_types/' . $topicType->getId(),
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertEquals('New Discussion Topic', $data['name']);
        $this->assertEquals(0, $data['nbMessage']); // New topic starts with 0 messages
    }

    public function testUpdateTopicRequiresOwnership(): void
    {
        AdminUserStory::load();
        $adminUser = AdminUserStory::adminUser();

        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $topic = TopicFactory::new()
            ->discussion()
            ->inForum($forum)
            ->byUser($adminUser)
            ->create();

        // Try to update without authentication
        $this->apiClient->request('PUT', '/api/forum_topics/' . $topic->getId(), [
            'json' => [
                'name' => 'Updated Topic Name',
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);

        // Authenticate as different user
        $otherUser = $this->createAuthenticatedUser();

        $this->apiClient->request('PUT', '/api/forum_topics/' . $topic->getId(), [
            'json' => [
                'name' => 'Updated Topic Name',
            ]
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testGetTopicsFilteredByForum(): void
    {
        $category = CategoryFactory::new()->create();
        $forum1 = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $forum2 = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        TopicFactory::new()
            ->inForum($forum1)
            ->many(3)
            ->create();

        TopicFactory::new()
            ->inForum($forum2)
            ->many(2)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topics?forum=' . $forum1->getId());

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertEquals(3, $data['hydra:totalItems']);
    }

    public function testGetTopicsExcludeArchived(): void
    {
        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        TopicFactory::new()
            ->active()
            ->inForum($forum)
            ->many(3)
            ->create();

        TopicFactory::new()
            ->archived()
            ->inForum($forum)
            ->many(2)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topics?boolArchive=false');

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertEquals(3, $data['hydra:totalItems']);
    }

    public function testGetTopicsOrderedByLastMessage(): void
    {
        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        TopicFactory::new()
            ->inForum($forum)
            ->many(3)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topics?order[lastMessage.id]=DESC');

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertGreaterThan(0, $data['hydra:totalItems']);
    }

    public function testTopicWithTypeRelation(): void
    {
        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $announcementType = TopicTypeFactory::new()
            ->announcement()
            ->create();

        $topic = TopicFactory::new()
            ->announcement()
            ->ofType($announcementType)
            ->inForum($forum)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topics/' . $topic->getId());

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertArrayHasKey('type', $data);
        $this->assertEquals('Annonce', $data['type']['name']);
    }
}
