<?php

declare(strict_types=1);

namespace App\BoundedContext\Forum\Tests\Factory;

use App\BoundedContext\Forum\Domain\Entity\Message;
use App\BoundedContext\User\Tests\Factory\UserFactory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Message>
 */
final class ForumMessageFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Message::class;
    }

    protected function defaults(): array|callable
    {
        return function () {
            return [
                'message' => self::faker()->paragraphs(3, true),
                'position' => self::faker()->numberBetween(1, 10),
                'topic' => TopicFactory::new(),
                'user' => UserFactory::new(),
            ];
        };
    }

    public function inTopic(object $topic): static
    {
        return $this->with([
            'topic' => $topic,
        ]);
    }

    public function byUser(object $user): static
    {
        return $this->with([
            'user' => $user,
        ]);
    }

    public function firstMessage(): static
    {
        return $this->with([
            'position' => 1,
        ]);
    }

    public function withPosition(int $position): static
    {
        return $this->with([
            'position' => $position,
        ]);
    }

    public function withContent(string $message): static
    {
        return $this->with([
            'message' => $message,
        ]);
    }

    public function shortMessage(): static
    {
        return $this->with([
            'message' => self::faker()->sentence(10),
        ]);
    }

    public function longMessage(): static
    {
        return $this->with([
            'message' => self::faker()->paragraphs(5, true),
        ]);
    }

    public function reply(): static
    {
        return $this->with([
            'position' => self::faker()->numberBetween(2, 20),
            'message' => 'Re: ' . self::faker()->paragraphs(2, true),
        ]);
    }

    public function announcement(): static
    {
        return $this->with([
            'position' => 1,
            'message' => '[b]Annonce importante:[/b] ' . self::faker()->paragraphs(2, true),
        ]);
    }

    public function withFormattedContent(): static
    {
        return $this->with([
            'message' => '[b]Message formaté[/b] avec du [i]texte en italique[/i] et des [u]soulignements[/u].',
        ]);
    }
}
