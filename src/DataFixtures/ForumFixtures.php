<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\BoundedContext\Forum\Tests\Story\ForumStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ForumFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Load basic forum structure using the ForumStory
        ForumStory::load();

        // Create references for other fixtures that might need them
        $generalForum = ForumStory::generalForum();
        $generalCategory = ForumStory::generalCategory();
        $announcementType = ForumStory::announcementType();

        $this->addReference('forum_general', $generalForum->_real());
        $this->addReference('category_general', $generalCategory->_real());
        $this->addReference('topic_type_announcement', $announcementType->_real());
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
