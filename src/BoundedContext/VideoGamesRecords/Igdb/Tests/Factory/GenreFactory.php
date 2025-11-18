<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Igdb\Tests\Factory;

use App\BoundedContext\VideoGamesRecords\Igdb\Domain\Entity\Genre;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Genre>
 */
final class GenreFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Genre::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'id' => self::faker()->unique()->randomNumber(4),
            'name' => self::faker()->words(2, true),
            'slug' => self::faker()->slug(),
            'url' => self::faker()->url(),
            'checksum' => self::faker()->uuid(),
            'createdAt' => self::faker()->dateTimeBetween('-1 year'),
            'updatedAt' => self::faker()->dateTimeBetween('-1 month'),
        ];
    }

    public function action(): static
    {
        return $this->with([
            'name' => 'Action',
            'slug' => 'action',
        ]);
    }

    public function rpg(): static
    {
        return $this->with([
            'name' => 'Role-playing (RPG)',
            'slug' => 'role-playing-rpg',
        ]);
    }

    public function adventure(): static
    {
        return $this->with([
            'name' => 'Adventure',
            'slug' => 'adventure',
        ]);
    }

    public function strategy(): static
    {
        return $this->with([
            'name' => 'Strategy',
            'slug' => 'strategy',
        ]);
    }

    public function shooter(): static
    {
        return $this->with([
            'name' => 'Shooter',
            'slug' => 'shooter',
        ]);
    }
}
