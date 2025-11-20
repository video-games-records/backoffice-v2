<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Igdb\Tests\Factory;

use App\BoundedContext\VideoGamesRecords\Igdb\Domain\Entity\Platform;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Platform>
 */
final class PlatformFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Platform::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'id' => self::faker()->unique()->randomNumber(4),
            'name' => self::faker()->words(2, true),
            'abbreviation' => self::faker()->optional()->lexify('???'),
            'alternativeName' => self::faker()->optional()->words(3, true),
            'generation' => self::faker()->optional()->numberBetween(1, 9),
            'slug' => self::faker()->slug(),
            'summary' => self::faker()->optional()->paragraph(),
            'url' => self::faker()->optional()->url(),
            'checksum' => self::faker()->uuid(),
            'platformType' => PlatformTypeFactory::new(),
            'platformLogo' => PlatformLogoFactory::new(),
            'createdAt' => self::faker()->dateTimeBetween('-1 year'),
            'updatedAt' => self::faker()->dateTimeBetween('-1 month'),
        ];
    }

    public function playstation(): static
    {
        return $this->with([
            'name' => 'PlayStation',
            'abbreviation' => 'PS',
            'slug' => 'playstation',
            'generation' => 1,
        ]);
    }

    public function playstation2(): static
    {
        return $this->with([
            'name' => 'PlayStation 2',
            'abbreviation' => 'PS2',
            'slug' => 'playstation-2',
            'generation' => 2,
        ]);
    }

    public function xbox(): static
    {
        return $this->with([
            'name' => 'Xbox',
            'abbreviation' => 'Xbox',
            'slug' => 'xbox',
            'generation' => 1,
        ]);
    }

    public function nintendo64(): static
    {
        return $this->with([
            'name' => 'Nintendo 64',
            'abbreviation' => 'N64',
            'slug' => 'nintendo-64',
            'generation' => 1,
        ]);
    }

    public function pc(): static
    {
        return $this->with([
            'name' => 'PC (Microsoft Windows)',
            'abbreviation' => 'PC',
            'slug' => 'pc-microsoft-windows',
            'generation' => null,
        ]);
    }

    public function withoutLogo(): static
    {
        return $this->with(['platformLogo' => null]);
    }

    public function withoutType(): static
    {
        return $this->with(['platformType' => null]);
    }
}
