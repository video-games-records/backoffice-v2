<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Tests\Factory;

use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game;
use App\BoundedContext\VideoGamesRecords\Core\Domain\ValueObject\GameStatus;
use DateTime;
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

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    protected function defaults(): array|callable
    {
        return [
            'libGameEn' => self::faker()->unique()->words(3, true),
            'libGameFr' => self::faker()->unique()->words(3, true),
            'downloadUrl' => self::faker()->optional()->url(),
            'status' => GameStatus::CREATED,
            'publishedAt' => null,
        ];
    }

    /**
     * Mark the game as published with a recent date
     */
    public function published(?DateTime $date = null): static
    {
        return $this->with([
            // Consider a published game as ACTIVE in our domain
            'status' => GameStatus::ACTIVE,
            'publishedAt' => $date ?? self::faker()->dateTimeBetween('-6 months'),
        ]);
    }

    /**
     * Mark the game as draft/unpublished
     */
    public function draft(): static
    {
        return $this->with([
            'status' => GameStatus::CREATED,
            'publishedAt' => null,
        ]);
    }

    /**
     * Override status
     */
    public function withStatus(string $status): static
    {
        return $this->with(['status' => $status]);
    }

    /**
     * Override names
     */
    public function withNames(string $en, ?string $fr = null): static
    {
        return $this->with([
            'libGameEn' => $en,
            'libGameFr' => $fr ?? $en,
        ]);
    }
}
