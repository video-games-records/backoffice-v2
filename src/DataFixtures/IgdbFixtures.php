<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\BoundedContext\VideoGamesRecords\Igdb\Tests\Story\IgdbStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IgdbFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Use the IgdbStory to create consistent IGDB data for both fixtures and tests
        IgdbStory::load();

        // Create references for other fixtures that might need them
        $pcPlatform = IgdbStory::pcPlatform();
        $playstationPlatform = IgdbStory::playstationPlatform();
        $actionGenre = IgdbStory::actionGenre();
        $rpgGenre = IgdbStory::rpgGenre();
        $consolePlatformType = IgdbStory::consolePlatformType();

        $this->addReference('igdb-platform-pc', $pcPlatform->_real());
        $this->addReference('igdb-platform-playstation', $playstationPlatform->_real());
        $this->addReference('igdb-genre-action', $actionGenre->_real());
        $this->addReference('igdb-genre-rpg', $rpgGenre->_real());
        $this->addReference('igdb-platform-type-console', $consolePlatformType->_real());
    }
}
