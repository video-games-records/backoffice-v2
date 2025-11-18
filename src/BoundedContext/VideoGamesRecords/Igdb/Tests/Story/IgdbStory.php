<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Igdb\Tests\Story;

use App\BoundedContext\VideoGamesRecords\Igdb\Tests\Factory\GameFactory;
use App\BoundedContext\VideoGamesRecords\Igdb\Tests\Factory\GenreFactory;
use App\BoundedContext\VideoGamesRecords\Igdb\Tests\Factory\PlatformFactory;
use App\BoundedContext\VideoGamesRecords\Igdb\Tests\Factory\PlatformLogoFactory;
use App\BoundedContext\VideoGamesRecords\Igdb\Tests\Factory\PlatformTypeFactory;
use Zenstruck\Foundry\Story;

final class IgdbStory extends Story
{
    public function build(): void
    {
        // Create platform types
        PlatformTypeFactory::new()->console()->create(['id' => 1]);
        PlatformTypeFactory::new()->computer()->create(['id' => 2]);
        PlatformTypeFactory::new()->arcade()->create(['id' => 3]);

        // Create platform logos
        PlatformLogoFactory::new()->create(['id' => 1]);
        PlatformLogoFactory::new()->large()->create(['id' => 2]);
        PlatformLogoFactory::new()->small()->create(['id' => 3]);
        PlatformLogoFactory::new()->animated()->create(['id' => 4]);

        // Create platforms with specific IDs to match typical IGDB data
        PlatformFactory::new()->pc()->withoutLogo()->create(['id' => 6]);
        PlatformFactory::new()->playstation()->create(['id' => 7]);
        PlatformFactory::new()->playstation2()->create(['id' => 8]);
        PlatformFactory::new()->xbox()->create(['id' => 11]);
        PlatformFactory::new()->nintendo64()->create(['id' => 4]);

        // Create genres with specific IDs to match typical IGDB data
        GenreFactory::new()->action()->create(['id' => 31]);
        GenreFactory::new()->rpg()->create(['id' => 12]);
        GenreFactory::new()->adventure()->create(['id' => 31]);
        GenreFactory::new()->strategy()->create(['id' => 15]);
        GenreFactory::new()->shooter()->create(['id' => 5]);

        // Create some games
        GameFactory::new()
            ->actionRpg()
            ->withMultiplePlatforms()
            ->create(['id' => 1]);

        GameFactory::new()
            ->platformer()
            ->retro()
            ->create(['id' => 2]);

        GameFactory::new()
            ->shooter()
            ->modern()
            ->create(['id' => 3]);

        // Create a game with version parent
        GameFactory::new()
            ->withVersionParent()
            ->create(['id' => 4]);

        // Create additional random games
        GameFactory::new()->many(5)->create();
    }

    /**
     * Get PC platform
     */
    public static function pcPlatform(): object
    {
        return PlatformFactory::find(['name' => 'PC (Microsoft Windows)']);
    }

    /**
     * Get PlayStation platform
     */
    public static function playstationPlatform(): object
    {
        return PlatformFactory::find(['name' => 'PlayStation']);
    }

    /**
     * Get Action genre
     */
    public static function actionGenre(): object
    {
        return GenreFactory::find(['name' => 'Action']);
    }

    /**
     * Get RPG genre
     */
    public static function rpgGenre(): object
    {
        return GenreFactory::find(['name' => 'Role-playing (RPG)']);
    }

    /**
     * Get Console platform type
     */
    public static function consolePlatformType(): object
    {
        return PlatformTypeFactory::find(['name' => 'Console']);
    }
}
