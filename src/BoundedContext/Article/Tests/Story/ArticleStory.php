<?php

declare(strict_types=1);

namespace App\BoundedContext\Article\Tests\Story;

use App\BoundedContext\Article\Tests\Factory\ArticleFactory;
use App\BoundedContext\Article\Tests\Factory\CommentFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;
use Zenstruck\Foundry\Story;

final class ArticleStory extends Story
{
    public function build(): void
    {
        // Ensure we have users
        AdminUserStory::load();

        $adminUser = AdminUserStory::adminUser();
        $regularUser = AdminUserStory::regularUser();
        $moderatorUser = AdminUserStory::moderatorUser();

        // Create featured articles
        ArticleFactory::new()
            ->published()
            ->popular()
            ->byAuthor($adminUser)
            ->create(['views' => 2500]);

        ArticleFactory::new()
            ->published()
            ->byAuthor($moderatorUser)
            ->create(['views' => 1800]);

        ArticleFactory::new()
            ->published()
            ->byAuthor($regularUser)
            ->create(['views' => 950]);

        // Create some draft articles
        ArticleFactory::new()
            ->draft()
            ->byAuthor($adminUser)
            ->create();

        // Create random published articles
        ArticleFactory::new()
            ->published()
            ->many(7)
            ->create();

        // Create some comments on the first article
        $featuredArticle = ArticleFactory::find(['views' => 2500]);

        CommentFactory::new()
            ->forArticle($featuredArticle)
            ->byUser($regularUser)
            ->withContent('Excellent article ! Très utile pour débuter avec Symfony.')
            ->create();

        CommentFactory::new()
            ->forArticle($featuredArticle)
            ->byUser($moderatorUser)
            ->withContent('Merci pour ce guide complet, cela m\'a beaucoup aidé.')
            ->create();

        // Create random comments
        CommentFactory::new()->many(15)->create();
    }

    public static function featuredArticle(): object
    {
        return ArticleFactory::find(['views' => 2500]);
    }

    public static function dddArticle(): object
    {
        return ArticleFactory::find(['views' => 1800]);
    }

    public static function apiPlatformArticle(): object
    {
        return ArticleFactory::find(['views' => 950]);
    }
}
