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
        PlatformFactory::new()->pc()->withoutLogo()->withoutType()->create(['id' => 1]);
        PlatformFactory::new()->playstation()->withoutLogo()->withoutType()->create(['id' => 2]);
        PlatformFactory::new()->playstation2()->withoutLogo()->withoutType()->create(['id' => 3]);
        PlatformFactory::new()->xbox()->withoutLogo()->withoutType()->create(['id' => 4]);
        PlatformFactory::new()->nintendo64()->withoutLogo()->withoutType()->create(['id' => 5]);

        // Create genres with specific IDs to match typical IGDB data
        GenreFactory::new()->action()->create(['id' => 1]);
        GenreFactory::new()->rpg()->create(['id' => 2]);
        GenreFactory::new()->adventure()->create(['id' => 3]);
        GenreFactory::new()->strategy()->create(['id' => 4]);
        GenreFactory::new()->shooter()->create(['id' => 5]);

        // Create some simple games without complex relationships
        GameFactory::new()
            ->withoutGenres()
            ->withoutPlatforms()
            ->create(['id' => 1, 'name' => 'Epic Action RPG', 'slug' => 'epic-action-rpg']);

        GameFactory::new()
            ->withoutGenres()
            ->withoutPlatforms()
            ->create(['id' => 2, 'name' => 'Super Platformer', 'slug' => 'super-platformer']);

        GameFactory::new()
            ->withoutGenres()
            ->withoutPlatforms()
            ->create(['id' => 3, 'name' => 'Space Shooter', 'slug' => 'space-shooter']);
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
