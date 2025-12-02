<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Load VideoGamesRecords test data (should be self-contained)
        \App\BoundedContext\VideoGamesRecords\Core\Tests\Story\DefaultPlatformStory::load();
        \App\BoundedContext\VideoGamesRecords\Core\Tests\Story\DefaultSerieStory::load();
        \App\BoundedContext\VideoGamesRecords\Core\Tests\Story\DefaultChartTypeStory::load();
        \App\BoundedContext\VideoGamesRecords\Core\Tests\Story\DefaultGameStory::load();
        \App\BoundedContext\VideoGamesRecords\Core\Tests\Story\DefaultGroupStory::load();
        \App\BoundedContext\VideoGamesRecords\Core\Tests\Story\DefaultChartStory::load();
        
        $manager->flush();
    }
}
