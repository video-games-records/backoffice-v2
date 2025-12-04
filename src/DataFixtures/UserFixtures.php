<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\BoundedContext\User\Tests\Story\GroupStory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Charger les groupes d'utilisateurs
        GroupStory::load();

        // Use the AdminUserStory to create consistent users for both fixtures and tests
        AdminUserStory::load();

        // Create references for other fixtures that might need them
        $adminUser = AdminUserStory::adminUser();
        $regularUser = AdminUserStory::regularUser();

        $this->addReference('user1', $adminUser->_real());
        $this->addReference('user2', $regularUser->_real());
        $this->addReference('group_player', GroupStory::player()->_real());
        $this->addReference('group_admin', GroupStory::admin()->_real());
    }

    public function getDependencies(): array
    {
        return [
            BadgeFixtures::class,
        ];
    }
}
