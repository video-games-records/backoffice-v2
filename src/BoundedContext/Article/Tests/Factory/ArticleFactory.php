<?php

declare(strict_types=1);

namespace App\BoundedContext\Article\Tests\Factory;

use App\BoundedContext\Article\Domain\Entity\Article;
use App\BoundedContext\Article\Domain\Entity\ArticleTranslation;
use App\BoundedContext\Article\Domain\ValueObject\ArticleStatus;
use App\BoundedContext\User\Domain\Entity\User;
use App\BoundedContext\User\Tests\Factory\UserFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Article>
 */
final class ArticleFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Article::class;
    }

    protected function defaults(): array
    {
        return [
            'status' => ArticleStatus::UNDER_CONSTRUCTION,
            'author' => UserFactory::new(),
            'publishedAt' => null,
            'views' => 0,
            'nbComment' => 0,
            'slug' => self::faker()->slug(),
            'createdAt' => new \DateTime(),
            'updatedAt' => new \DateTime(),
        ];
    }

    protected function initialize(): static
    {
        return $this->afterInstantiate(function (Article $article) {
            // Only add translations if none exist
            if ($article->getTranslations()->isEmpty()) {
                // Create French translation
                $frTranslation = new ArticleTranslation();
                $frTranslation->setTranslatable($article);
                $frTranslation->setLocale('fr');
                $frTranslation->setTitle(self::faker()->sentence(6));
                $frTranslation->setContent(self::faker()->paragraphs(3, true));
                $article->addTranslation($frTranslation);

                // Create English translation
                $enTranslation = new ArticleTranslation();
                $enTranslation->setTranslatable($article);
                $enTranslation->setLocale('en');
                $enTranslation->setTitle(self::faker()->sentence(6));
                $enTranslation->setContent(self::faker()->paragraphs(3, true));
                $article->addTranslation($enTranslation);
            }
        });
    }

    /**
     * Create a published article
     */
    public function published(): static
    {
        return $this->with([
            'status' => ArticleStatus::PUBLISHED,
            'publishedAt' => self::faker()->dateTimeBetween('-6 months'),
        ]);
    }

    /**
     * Create a draft article
     */
    public function draft(): static
    {
        return $this->with([
            'status' => ArticleStatus::UNDER_CONSTRUCTION,
            'publishedAt' => null,
        ]);
    }

    /**
     * Create a popular article with many views
     */
    public function popular(): static
    {
        return $this->with([
            'status' => ArticleStatus::PUBLISHED,
            'publishedAt' => self::faker()->dateTimeBetween('-3 months'),
            'views' => self::faker()->numberBetween(1000, 10000),
        ]);
    }


    /**
     * Create article by specific author
     */
    public function byAuthor(User $author): static
    {
        return $this->with([
            'author' => $author,
        ]);
    }

    /**
     * Create article with specific translations
     */
    public function withTranslations(string $frTitle, string $frContent, string $enTitle, string $enContent): static
    {
        return $this->afterInstantiate(function (Article $article) use ($frTitle, $frContent, $enTitle, $enContent) {
            // Clear existing translations first
            $article->getTranslations()->clear();

            // Create French translation
            $frTranslation = new ArticleTranslation();
            $frTranslation->setTranslatable($article);
            $frTranslation->setLocale('fr');
            $frTranslation->setTitle($frTitle);
            $frTranslation->setContent($frContent);
            $article->addTranslation($frTranslation);

            // Create English translation
            $enTranslation = new ArticleTranslation();
            $enTranslation->setTranslatable($article);
            $enTranslation->setLocale('en');
            $enTranslation->setTitle($enTitle);
            $enTranslation->setContent($enContent);
            $article->addTranslation($enTranslation);
        });
    }
}
