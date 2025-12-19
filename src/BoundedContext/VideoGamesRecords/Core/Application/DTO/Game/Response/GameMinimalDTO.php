<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Game\Response;

class GameMinimalDTO
{
    /**
     * @param array<int, array<string, mixed>> $platforms
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly array $platforms,
    ) {
    }
}
