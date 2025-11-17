<?php

declare(strict_types=1);

namespace App\BoundedContext\Forum\Tests\Story;

use App\BoundedContext\Forum\Tests\Factory\CategoryFactory;
use App\BoundedContext\Forum\Tests\Factory\ForumFactory;
use App\BoundedContext\Forum\Tests\Factory\ForumMessageFactory;
use App\BoundedContext\Forum\Tests\Factory\TopicFactory;
use App\BoundedContext\Forum\Tests\Factory\TopicTypeFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;
use Zenstruck\Foundry\Story;

final class ForumStory extends Story
{
    public function build(): void
    {
        // Create basic structure only - no topics/messages for now
        $announcementType = TopicTypeFactory::new()->announcement()->create();
        $discussionType = TopicTypeFactory::new()->discussion()->create();
        $questionType = TopicTypeFactory::new()->question()->create();

        $generalCategory = CategoryFactory::new()->general()->create();
        $helpCategory = CategoryFactory::new()->help()->create();
        $announcementsCategory = CategoryFactory::new()->announcements()->create();

        $generalForum = ForumFactory::new()
            ->general()
            ->withCategory($generalCategory)
            ->withStats(0, 0)
            ->create();

        $announcementsForum = ForumFactory::new()
            ->announcements()
            ->withCategory($announcementsCategory)
            ->withStats(0, 0)
            ->create();

        ForumFactory::new()
            ->publicForum()
            ->withCategory($helpCategory)
            ->withStats(0, 0)
            ->many(2)
            ->create();
    }

    public static function generalForum(): object
    {
        return ForumFactory::findOrCreate(['libForum' => 'Forum Général']);
    }

    public static function generalCategory(): object
    {
        return CategoryFactory::findOrCreate(['name' => 'Général']);
    }

    public static function announcementType(): object
    {
        return TopicTypeFactory::findOrCreate(['name' => 'Annonce']);
    }
}
