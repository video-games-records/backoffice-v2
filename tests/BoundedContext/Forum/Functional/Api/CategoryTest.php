<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\Forum\Functional\Api;

use App\BoundedContext\Forum\Domain\Entity\Category;
use App\BoundedContext\Forum\Tests\Factory\CategoryFactory;
use App\BoundedContext\Forum\Tests\Factory\ForumFactory;

class CategoryTest extends AbstractFunctionalTestCase
{
    public function testGetCollection(): void
    {
        CategoryFactory::new()
            ->many(3)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_categories');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Category::class);

        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:totalItems', $data);
        $this->assertEquals(3, $data['hydra:totalItems']);
    }

    public function testGetSingleCategory(): void
    {
        $category = CategoryFactory::new()
            ->general()
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_categories/' . $category->getId());

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Category::class);

        $data = $response->toArray();
        $this->assertEquals($category->getId(), $data['id']);
        $this->assertEquals('Général', $data['name']);
    }

    public function testGetHomeCategories(): void
    {
        // Test basic endpoint functionality - categories may come from fixtures
        $response = $this->apiClient->request('GET', '/api/forum_category/get-home');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:member', $data);
        $this->assertIsArray($data['hydra:member']);
    }
}
