<?php

declare(strict_types=1);

namespace App\BoundedContext\Forum\Tests\Factory;

use App\BoundedContext\Forum\Domain\Entity\TopicType;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<TopicType>
 */
final class TopicTypeFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return TopicType::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->randomElement(['Annonce', 'Discussion', 'Question', 'Tutoriel', 'Bug Report']),
            'position' => self::faker()->numberBetween(0, 10),
        ];
    }

    public function announcement(): static
    {
        return $this->with([
            'name' => 'Annonce',
            'position' => 0,
        ]);
    }

    public function discussion(): static
    {
        return $this->with([
            'name' => 'Discussion',
            'position' => 1,
        ]);
    }

    public function question(): static
    {
        return $this->with([
            'name' => 'Question',
            'position' => 2,
        ]);
    }
}
