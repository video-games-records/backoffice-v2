<?php

declare(strict_types=1);

namespace App\BoundedContext\Message\Tests\Story;

use App\BoundedContext\Message\Tests\Factory\MessageFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;
use Zenstruck\Foundry\Story;

final class MessageStory extends Story
{
    public function build(): void
    {
        // Ensure we have users
        AdminUserStory::load();

        $adminUser = AdminUserStory::adminUser();
        $regularUser = AdminUserStory::regularUser();
        $moderatorUser = AdminUserStory::moderatorUser();

        // Create some conversation between admin and regular user
        MessageFactory::new()
            ->between($adminUser, $regularUser)
            ->withContent('Bienvenue sur la plateforme!', 'Bonjour et bienvenue sur notre plateforme de gestion. N\'hésitez pas si vous avez des questions.')
            ->info()
            ->opened()
            ->create();

        MessageFactory::new()
            ->between($regularUser, $adminUser)
            ->withContent('Merci pour l\'accueil', 'Merci beaucoup pour ce message de bienvenue. J\'ai hâte de découvrir toutes les fonctionnalités.')
            ->unread()
            ->create();

        // Urgent message from moderator to admin
        MessageFactory::new()
            ->between($moderatorUser, $adminUser)
            ->urgent()
            ->withContent('Problème urgent sur le site', 'Il y a un problème technique qui nécessite votre attention immédiate.')
            ->unread()
            ->create();

        // Create some inbox messages for regular user (received)
        MessageFactory::new()
            ->toRecipient($regularUser)
            ->fromSender($adminUser)
            ->info()
            ->unread()
            ->many(3)
            ->create();

        MessageFactory::new()
            ->toRecipient($regularUser)
            ->fromSender($moderatorUser)
            ->opened()
            ->many(2)
            ->create();

        // Create some outbox messages for regular user (sent)
        MessageFactory::new()
            ->fromSender($regularUser)
            ->toRecipient($adminUser)
            ->opened()
            ->many(2)
            ->create();

        MessageFactory::new()
            ->fromSender($regularUser)
            ->toRecipient($moderatorUser)
            ->unread()
            ->create();

        // Create some deleted messages
        MessageFactory::new()
            ->between($adminUser, $regularUser)
            ->deletedBySender()
            ->create();

        MessageFactory::new()
            ->between($regularUser, $adminUser)
            ->deletedByRecipient()
            ->create();

        // Create random messages for testing pagination
        MessageFactory::new()
            ->many(15)
            ->create();
    }

    public static function welcomeMessage(): object
    {
        return MessageFactory::findOrCreate(['object' => 'Bienvenue sur la plateforme!']);
    }

    public static function urgentMessage(): object
    {
        return MessageFactory::findOrCreate(['type' => 'URGENT']);
    }

    public static function unreadMessage(): object
    {
        return MessageFactory::findOrCreate(['isOpened' => false]);
    }
}
