<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Tests\Factory;

use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChartStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PlayerChartStatus>
 */
final class PlayerChartStatusFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return PlayerChartStatus::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    protected function defaults(): array|callable
    {
        return [
            'class' => 'default',
            'boolRanking' => true,
        ];
    }

    /**
     * Set the CSS class
     */
    public function withClass(string $class): static
    {
        return $this->with(['class' => $class]);
    }

    /**
     * Set ranking visibility
     */
    public function withRanking(bool $boolRanking): static
    {
        return $this->with(['boolRanking' => $boolRanking]);
    }

    /**
     * Disable ranking
     */
    public function noRanking(): static
    {
        return $this->with(['boolRanking' => false]);
    }
}