<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\Forum\Functional\Api;

use App\BoundedContext\Forum\Domain\Entity\Forum;
use App\BoundedContext\Forum\Tests\Factory\CategoryFactory;
use App\BoundedContext\Forum\Tests\Factory\ForumFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;

class ForumTest extends AbstractFunctionalTestCase
{
    public function testGetCollectionPublicForums(): void
    {
        // Create some public and private forums
        $category = CategoryFactory::new()->general()->create();

        ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->many(3)
            ->create();

        ForumFactory::new()
            ->privateForum()
            ->withCategory($category)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_forums');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Forum::class);

        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:totalItems', $data);
        // Should have 4 forums (3 public + 1 private)
        $this->assertEquals(4, $data['hydra:totalItems']);
    }

    public function testGetForumRequiresProperAccess(): void
    {
        // Create a private forum that requires admin role
        $category = CategoryFactory::new()->create();
        $privateForum = ForumFactory::new()
            ->privateForum()
            ->withRole('ROLE_ADMIN')
            ->withCategory($category)
            ->create();

        // Test without authentication - should fail
        $this->apiClient->request('GET', '/api/forum_forums/' . $privateForum->getId());
        $this->assertResponseStatusCodeSame(401);

        // Test with regular user authentication
        $regularUser = $this->createAuthenticatedUser(['ROLE_USER']);
        $this->apiClient->request('GET', '/api/forum_forums/' . $privateForum->getId());
        $this->assertResponseStatusCodeSame(403);

        // Test with admin user - should work
        $this->createAdminUser();
        $response = $this->apiClient->request('GET', '/api/forum_forums/' . $privateForum->getId());
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Forum::class);
    }

    public function testGetPublicForumWithoutAuthentication(): void
    {
        $category = CategoryFactory::new()->create();
        $publicForum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_forums/' . $publicForum->getId());

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Forum::class);

        $data = $response->toArray();
        $this->assertEquals($publicForum->getId(), $data['id']);
    }

    public function testMarkAllForumsAsReadRequiresAuthentication(): void
    {
        $this->apiClient->request('POST', '/api/forum_forums/read-all', [
            'json' => []
        ]);
        $this->assertResponseStatusCodeSame(401);
    }

    public function testMarkAllForumsAsRead(): void
    {
        $this->createAuthenticatedUser();

        $response = $this->apiClient->request('POST', '/api/forum_forums/read-all', [
            'json' => []
        ]);
        $this->assertResponseIsSuccessful();
    }

    public function testGetForumStats(): void
    {
        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->withStats(10, 50)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_forums/' . $forum->getId() . '/stats');

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertArrayHasKey('nbTopic', $data);
        $this->assertArrayHasKey('nbMessage', $data);
    }

    public function testMarkForumAsReadRequiresAuthentication(): void
    {
        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $this->apiClient->request('GET', '/api/forum_forums/' . $forum->getId() . '/mark-as-read');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testMarkForumAsRead(): void
    {
        $this->createAuthenticatedUser();

        $category = CategoryFactory::new()->create();
        $forum = ForumFactory::new()
            ->publicForum()
            ->withCategory($category)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_forums/' . $forum->getId() . '/mark-as-read');
        $this->assertResponseIsSuccessful();
    }

    public function testGetForumsFilteredByCategory(): void
    {
        // Use unique category names to avoid conflicts with fixtures
        $testCategory = CategoryFactory::new()
            ->with(['name' => 'TestCategory_' . uniqid()])
            ->create();

        $otherCategory = CategoryFactory::new()
            ->with(['name' => 'OtherCategory_' . uniqid()])
            ->create();

        ForumFactory::new()
            ->publicForum()
            ->withCategory($testCategory)
            ->many(2)
            ->create();

        ForumFactory::new()
            ->publicForum()
            ->withCategory($otherCategory)
            ->create();

        // Test filtering by category - should have exactly 2
        $response = $this->apiClient->request('GET', '/api/forum_forums?category=' . $testCategory->getId());

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertEquals(2, $data['hydra:totalItems']);
    }
}
