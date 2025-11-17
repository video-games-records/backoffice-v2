<?php

declare(strict_types=1);

namespace App\BoundedContext\Forum\Tests\Factory;

use App\BoundedContext\Forum\Domain\Entity\Topic;
use App\BoundedContext\User\Tests\Factory\UserFactory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Topic>
 */
final class TopicFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Topic::class;
    }

    protected function defaults(): array|callable
    {
        return function () {
            return [
                'name' => self::faker()->sentence(5),
                'nbMessage' => self::faker()->numberBetween(1, 50),
                'forum' => ForumFactory::new(),
                'user' => UserFactory::new(),
                'type' => TopicTypeFactory::new(),
                'boolArchive' => false,
            ];
        };
    }

    public function announcement(): static
    {
        return $this->with([
            'type' => TopicTypeFactory::new()->announcement(),
            'name' => 'Important: ' . self::faker()->sentence(4),
        ]);
    }

    public function discussion(): static
    {
        return $this->with([
            'type' => TopicTypeFactory::new()->discussion(),
        ]);
    }

    public function question(): static
    {
        return $this->with([
            'type' => TopicTypeFactory::new()->question(),
            'name' => 'Question: ' . self::faker()->sentence(4),
        ]);
    }

    public function inForum(object $forum): static
    {
        return $this->with([
            'forum' => $forum,
        ]);
    }

    public function byUser(object $user): static
    {
        return $this->with([
            'user' => $user,
        ]);
    }

    public function archived(): static
    {
        return $this->with([
            'boolArchive' => true,
        ]);
    }

    public function active(): static
    {
        return $this->with([
            'boolArchive' => false,
        ]);
    }

    public function withMessageCount(int $messageCount): static
    {
        return $this->with([
            'nbMessage' => $messageCount,
        ]);
    }

    public function popular(): static
    {
        return $this->with([
            'nbMessage' => self::faker()->numberBetween(20, 100),
        ]);
    }

    public function newTopic(): static
    {
        return $this->with([
            'nbMessage' => 1,
        ]);
    }

    public function withContent(string $name): static
    {
        return $this->with([
            'name' => $name,
        ]);
    }

    public function ofType(object $type): static
    {
        return $this->with([
            'type' => $type,
        ]);
    }
}
