<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\Article\Functional\Api;

use App\BoundedContext\Article\Domain\Entity\Article;
use App\BoundedContext\Article\Tests\Factory\ArticleFactory;
use App\BoundedContext\User\Tests\Factory\UserFactory;
use App\Tests\SharedKernel\Domain\Service\TestLocaleResolver;

class ArticleTest extends AbstractFunctionalTestCase
{
    public function testGetCollection(): void
    {
        $response = $this->apiClient->request('GET', '/api/articles');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Article::class);
    }

    public function testGetItem(): void
    {
        // Create minimal test data
        $user = UserFactory::new()->create();
        $article = ArticleFactory::new()
            ->published()
            ->byAuthor($user)
            ->create();

        $response = $this->apiClient->request('GET', '/api/articles/' . $article->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Article::class);

        $data = $response->toArray();
        $this->assertEquals('Article', $data['@type']);
        $this->assertEquals($article->getId(), $data['id']);
        $this->assertNotEmpty($data['title']);
        $this->assertNotEmpty($data['content']);
    }

    public function testGetItemInFrench(): void
    {
        $user = UserFactory::new()->create();
        $article = ArticleFactory::new()
            ->published()
            ->byAuthor($user)
            ->withTranslations(
                'Article Test Français',
                'Contenu en français',
                'English Test Article',
                'Content in English'
            )
            ->create();

        // Force locale for testing
        TestLocaleResolver::forceLocale('fr');

        $response = $this->apiClient->request('GET', '/api/articles/' . $article->getId());

        // Clean up
        TestLocaleResolver::clearForcedLocale();

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();

        $this->assertStringContainsString('Français', $data['title']);
        $this->assertStringContainsString('français', $data['content']);
    }

    public function testGetItemInEnglish(): void
    {
        $user = UserFactory::new()->create();
        $article = ArticleFactory::new()
            ->published()
            ->byAuthor($user)
            ->withTranslations(
                'Article Test Français',
                'Contenu en français',
                'English Test Article',
                'Content in English'
            )
            ->create();

        $response = $this->apiClient->request('GET', '/api/articles/' . $article->getId(), [
            'headers' => [
                'Accept-Language' => 'en'
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();

        $this->assertStringContainsString('English', $data['title']);
        $this->assertStringContainsString('English', $data['content']);
    }

    public function testGetItemNotFound(): void
    {
        $this->apiClient->request('GET', '/api/articles/999999');
        $this->assertResponseStatusCodeSame(404);
    }
}
