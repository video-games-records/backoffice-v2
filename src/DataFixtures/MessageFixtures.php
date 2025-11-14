<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\BoundedContext\Message\Tests\Story\MessageStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Load messages using the MessageStory
        MessageStory::load();

        // Create references for other fixtures that might need them
        $welcomeMessage = MessageStory::welcomeMessage();
        $urgentMessage = MessageStory::urgentMessage();

        $this->addReference('message_welcome', $welcomeMessage->_real());
        $this->addReference('message_urgent', $urgentMessage->_real());
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
