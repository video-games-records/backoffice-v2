<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Igdb\Tests\Factory;

use App\BoundedContext\VideoGamesRecords\Igdb\Domain\Entity\PlatformLogo;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PlatformLogo>
 */
final class PlatformLogoFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return PlatformLogo::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'id' => self::faker()->unique()->randomNumber(4),
            'alphaChannel' => self::faker()->boolean(),
            'animated' => self::faker()->boolean(),
            'checksum' => self::faker()->uuid(),
            'height' => self::faker()->numberBetween(50, 300),
            'imageId' => self::faker()->randomNumber(6),
            'url' => self::faker()->imageUrl(200, 100, 'logo'),
            'width' => self::faker()->numberBetween(100, 400),
            'createdAt' => self::faker()->dateTimeBetween('-1 year'),
            'updatedAt' => self::faker()->dateTimeBetween('-1 month'),
        ];
    }

    public function large(): static
    {
        return $this->with([
            'width' => self::faker()->numberBetween(300, 500),
            'height' => self::faker()->numberBetween(200, 350),
        ]);
    }

    public function small(): static
    {
        return $this->with([
            'width' => self::faker()->numberBetween(50, 150),
            'height' => self::faker()->numberBetween(30, 100),
        ]);
    }

    public function withAlpha(): static
    {
        return $this->with([
            'alphaChannel' => true,
        ]);
    }

    public function animated(): static
    {
        return $this->with([
            'animated' => true,
        ]);
    }
}
