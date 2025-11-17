<?php

declare(strict_types=1);

namespace App\BoundedContext\Forum\Tests\Factory;

use App\BoundedContext\Forum\Domain\Entity\Forum;
use App\BoundedContext\Forum\Domain\ValueObject\ForumStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Forum>
 */
final class ForumFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Forum::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'libForum' => self::faker()->sentence(3),
            'libForumFr' => self::faker()->sentence(3),
            'position' => self::faker()->numberBetween(0, 10),
            'status' => self::faker()->randomElement([ForumStatus::PUBLIC, ForumStatus::PRIVATE]),
            'role' => self::faker()->optional(0.3)->randomElement(['ROLE_ADMIN', 'ROLE_MODERATOR', 'ROLE_USER']),
            'nbMessage' => self::faker()->numberBetween(0, 1000),
            'nbTopic' => self::faker()->numberBetween(0, 100),
            'category' => CategoryFactory::new(),
        ];
    }

    public function publicForum(): static
    {
        return $this->with([
            'status' => ForumStatus::PUBLIC,
            'role' => null,
        ]);
    }

    public function privateForum(): static
    {
        return $this->with([
            'status' => ForumStatus::PRIVATE,
            'role' => 'ROLE_ADMIN',
        ]);
    }

    public function general(): static
    {
        return $this->with([
            'libForum' => 'Forum Général',
            'libForumFr' => 'Forum Général',
            'position' => 0,
            'status' => ForumStatus::PUBLIC,
            'role' => null,
        ]);
    }

    public function announcements(): static
    {
        return $this->with([
            'libForum' => 'Annonces',
            'libForumFr' => 'Annonces',
            'position' => 1,
            'status' => ForumStatus::PUBLIC,
            'role' => null,
        ]);
    }

    public function administration(): static
    {
        return $this->with([
            'libForum' => 'Administration',
            'libForumFr' => 'Administration',
            'position' => 10,
            'status' => ForumStatus::PRIVATE,
            'role' => 'ROLE_ADMIN',
        ]);
    }

    public function withCategory(object $category): static
    {
        return $this->with([
            'category' => $category,
        ]);
    }

    public function withPosition(int $position): static
    {
        return $this->with([
            'position' => $position,
        ]);
    }

    public function withStats(int $nbTopic, int $nbMessage): static
    {
        return $this->with([
            'nbTopic' => $nbTopic,
            'nbMessage' => $nbMessage,
        ]);
    }

    public function withRole(string $role): static
    {
        return $this->with([
            'role' => $role,
            'status' => ForumStatus::PRIVATE,
        ]);
    }

    public function active(): static
    {
        return $this->with([
            'nbTopic' => self::faker()->numberBetween(10, 100),
            'nbMessage' => self::faker()->numberBetween(50, 1000),
        ]);
    }

    public function inactive(): static
    {
        return $this->with([
            'nbTopic' => 0,
            'nbMessage' => 0,
        ]);
    }
}
