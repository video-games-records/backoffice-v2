<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\BoundedContext\Article\Tests\Story\ArticleStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Load articles using the ArticleStory
        ArticleStory::load();

        // Create references for other fixtures that might need them
        $featuredArticle = ArticleStory::featuredArticle();
        $dddArticle = ArticleStory::dddArticle();
        $apiPlatformArticle = ArticleStory::apiPlatformArticle();

        $this->addReference('article_featured', $featuredArticle->_real());
        $this->addReference('article_ddd', $dddArticle->_real());
        $this->addReference('article_api_platform', $apiPlatformArticle->_real());
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
