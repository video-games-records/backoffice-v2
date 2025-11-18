<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Igdb\Tests\Factory;

use App\BoundedContext\VideoGamesRecords\Igdb\Domain\Entity\Game;
use Doctrine\Common\Collections\ArrayCollection;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Game>
 */
final class GameFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Game::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'id' => self::faker()->unique()->randomNumber(5),
            'name' => self::faker()->words(3, true),
            'slug' => self::faker()->slug(),
            'storyline' => self::faker()->optional()->paragraphs(2, true),
            'summary' => self::faker()->optional()->paragraph(),
            'url' => self::faker()->optional()->url(),
            'checksum' => self::faker()->uuid(),
            'firstReleaseDate' => self::faker()->optional()->unixTime(),
            'genres' => new ArrayCollection([
                GenreFactory::new()->create()
            ]),
            'platforms' => new ArrayCollection([
                PlatformFactory::new()->create()
            ]),
            'createdAt' => self::faker()->dateTimeBetween('-1 year'),
            'updatedAt' => self::faker()->dateTimeBetween('-1 month'),
        ];
    }

    public function withVersionParent(): static
    {
        return $this->with([
            'versionParent' => GameFactory::new(),
        ]);
    }

    public function withMultipleGenres(): static
    {
        return $this->with([
            'genres' => new ArrayCollection([
                GenreFactory::new()->action()->create(),
                GenreFactory::new()->rpg()->create(),
            ]),
        ]);
    }

    public function withMultiplePlatforms(): static
    {
        return $this->with([
            'platforms' => new ArrayCollection([
                PlatformFactory::new()->pc()->create(),
                PlatformFactory::new()->playstation()->create(),
            ]),
        ]);
    }

    public function actionRpg(): static
    {
        return $this->with([
            'name' => 'Epic Action RPG',
            'slug' => 'epic-action-rpg',
            'genres' => new ArrayCollection([
                GenreFactory::new()->action()->create(),
                GenreFactory::new()->rpg()->create(),
            ]),
        ]);
    }

    public function platformer(): static
    {
        return $this->with([
            'name' => 'Super Platformer',
            'slug' => 'super-platformer',
            'genres' => new ArrayCollection([
                GenreFactory::new()->action()->create(),
            ]),
        ]);
    }

    public function shooter(): static
    {
        return $this->with([
            'name' => 'Space Shooter',
            'slug' => 'space-shooter',
            'genres' => new ArrayCollection([
                GenreFactory::new()->shooter()->create(),
            ]),
        ]);
    }

    public function retro(): static
    {
        return $this->with([
            'firstReleaseDate' => self::faker()->dateTimeBetween('-30 years', '-20 years')->getTimestamp(),
            'platforms' => new ArrayCollection([
                PlatformFactory::new()->nintendo64()->create(),
            ]),
        ]);
    }

    public function modern(): static
    {
        return $this->with([
            'firstReleaseDate' => self::faker()->dateTimeBetween('-5 years')->getTimestamp(),
            'platforms' => new ArrayCollection([
                PlatformFactory::new()->pc()->create(),
            ]),
        ]);
    }

    public function withoutGenres(): static
    {
        return $this->with([
            'genres' => new ArrayCollection(),
        ]);
    }

    public function withoutPlatforms(): static
    {
        return $this->with([
            'platforms' => new ArrayCollection(),
        ]);
    }
}
