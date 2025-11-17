<?php

declare(strict_types=1);

namespace App\BoundedContext\Forum\Tests\Factory;

use App\BoundedContext\Forum\Domain\Entity\Category;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Category>
 */
final class CategoryFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Category::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->randomElement(['Général', 'Discussions', 'Aide', 'Annonces', 'Technique']),
            'position' => self::faker()->numberBetween(0, 10),
            'displayOnHome' => self::faker()->boolean(80), // 80% chance d'être affiché sur l'accueil
        ];
    }

    public function general(): static
    {
        return $this->with([
            'name' => 'Général',
            'position' => 0,
            'displayOnHome' => true,
        ]);
    }

    public function discussions(): static
    {
        return $this->with([
            'name' => 'Discussions',
            'position' => 1,
            'displayOnHome' => true,
        ]);
    }

    public function help(): static
    {
        return $this->with([
            'name' => 'Aide',
            'position' => 2,
            'displayOnHome' => true,
        ]);
    }

    public function announcements(): static
    {
        return $this->with([
            'name' => 'Annonces',
            'position' => 3,
            'displayOnHome' => true,
        ]);
    }

    public function technical(): static
    {
        return $this->with([
            'name' => 'Technique',
            'position' => 4,
            'displayOnHome' => false,
        ]);
    }

    public function withPosition(int $position): static
    {
        return $this->with([
            'position' => $position,
        ]);
    }

    public function hidden(): static
    {
        return $this->with([
            'displayOnHome' => false,
        ]);
    }

    public function visible(): static
    {
        return $this->with([
            'displayOnHome' => true,
        ]);
    }
}
