<?php

declare(strict_types=1);

namespace App\BoundedContext\Message\Tests\Factory;

use App\BoundedContext\Message\Domain\Entity\Message;
use App\BoundedContext\User\Tests\Factory\UserFactory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Message>
 */
final class MessageFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Message::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'object' => self::faker()->sentence(6),
            'message' => self::faker()->paragraphs(3, true),
            'type' => self::faker()->randomElement(['DEFAULT', 'URGENT', 'INFO', 'WARNING']),
            'sender' => UserFactory::new(),
            'recipient' => UserFactory::new(),
            'isOpened' => self::faker()->boolean(30), // 30% chance d'être ouvert
            'isDeletedSender' => false,
            'isDeletedRecipient' => false,
        ];
    }

    /**
     * Create a message from specific sender
     */
    public function fromSender(object $sender): static
    {
        return $this->with([
            'sender' => $sender,
        ]);
    }

    /**
     * Create a message to specific recipient
     */
    public function toRecipient(object $recipient): static
    {
        return $this->with([
            'recipient' => $recipient,
        ]);
    }

    /**
     * Create a message between two specific users
     */
    public function between(object $sender, object $recipient): static
    {
        return $this->with([
            'sender' => $sender,
            'recipient' => $recipient,
        ]);
    }

    /**
     * Create an opened message
     */
    public function opened(): static
    {
        return $this->with([
            'isOpened' => true,
        ]);
    }

    /**
     * Create an unopened message
     */
    public function unread(): static
    {
        return $this->with([
            'isOpened' => false,
        ]);
    }

    /**
     * Create an urgent message
     */
    public function urgent(): static
    {
        return $this->with([
            'type' => 'URGENT',
            'object' => 'URGENT: ' . self::faker()->sentence(4),
        ]);
    }

    /**
     * Create an info message
     */
    public function info(): static
    {
        return $this->with([
            'type' => 'INFO',
            'object' => 'Info: ' . self::faker()->sentence(4),
        ]);
    }

    /**
     * Create a message deleted by sender
     */
    public function deletedBySender(): static
    {
        return $this->with([
            'isDeletedSender' => true,
        ]);
    }

    /**
     * Create a message deleted by recipient
     */
    public function deletedByRecipient(): static
    {
        return $this->with([
            'isDeletedRecipient' => true,
        ]);
    }

    /**
     * Create a message with specific content
     */
    public function withContent(string $object, string $message): static
    {
        return $this->with([
            'object' => $object,
            'message' => $message,
        ]);
    }

    /**
     * Create a message of specific type
     */
    public function ofType(string $type): static
    {
        return $this->with([
            'type' => $type,
        ]);
    }
}
