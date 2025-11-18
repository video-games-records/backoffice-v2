<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Igdb\Tests\Factory;

use App\BoundedContext\VideoGamesRecords\Igdb\Domain\Entity\PlatformType;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PlatformType>
 */
final class PlatformTypeFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return PlatformType::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'id' => self::faker()->unique()->randomNumber(3),
            'name' => self::faker()->words(2, true),
            'checksum' => self::faker()->uuid(),
            'createdAt' => self::faker()->dateTimeBetween('-1 year'),
            'updatedAt' => self::faker()->dateTimeBetween('-1 month'),
        ];
    }

    public function console(): static
    {
        return $this->with([
            'name' => 'Console',
        ]);
    }

    public function arcade(): static
    {
        return $this->with([
            'name' => 'Arcade',
        ]);
    }

    public function platform(): static
    {
        return $this->with([
            'name' => 'Platform',
        ]);
    }

    public function computer(): static
    {
        return $this->with([
            'name' => 'Computer',
        ]);
    }
}
