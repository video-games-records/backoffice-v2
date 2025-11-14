<?php

declare(strict_types=1);

namespace App\BoundedContext\Article\Tests\Factory;

use App\BoundedContext\Article\Domain\Entity\Article;
use App\BoundedContext\Article\Domain\Entity\Comment;
use App\BoundedContext\User\Domain\Entity\User;
use App\BoundedContext\User\Tests\Factory\UserFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Comment>
 */
final class CommentFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Comment::class;
    }

    protected function defaults(): array|callable
    {
        return function () {
            return [
                'user' => UserFactory::new()->create(), // Create actual user instance
                'article' => ArticleFactory::new()->create(), // Create actual article instance
                'content' => self::faker()->paragraphs(2, true),
                'createdAt' => self::faker()->dateTimeBetween('-6 months'),
                'updatedAt' => self::faker()->dateTimeBetween('-1 month'),
            ];
        };
    }

    /**
     * Create comment for specific article
     */
    public function forArticle(Article $article): static
    {
        return $this->with([
            'article' => $article,
        ]);
    }

    /**
     * Create comment by specific user
     */
    public function byUser(User $user): static
    {
        return $this->with([
            'user' => $user,
        ]);
    }

    /**
     * Create comment with specific content
     */
    public function withContent(string $content): static
    {
        return $this->with([
            'content' => $content,
        ]);
    }
}
