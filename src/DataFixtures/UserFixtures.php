<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\BoundedContext\User\Tests\Story\AdminUserStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Use the AdminUserStory to create consistent users for both fixtures and tests
        AdminUserStory::load();

        // Create references for other fixtures that might need them
        $adminUser = AdminUserStory::adminUser();
        $regularUser = AdminUserStory::regularUser();

        $this->addReference('user1', $adminUser->_real());
        $this->addReference('user2', $regularUser->_real());
    }
}
