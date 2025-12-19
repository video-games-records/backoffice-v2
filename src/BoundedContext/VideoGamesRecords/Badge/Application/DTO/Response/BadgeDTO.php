<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Badge\Application\DTO\Response;

class BadgeDTO
{
    /**
     * @param array<string, mixed>|null $game
     * @param array<string, mixed>|null $serie
     * @param array<string, mixed>|null $platform
     */
    public function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly string $picture,
        public readonly int $value,
        public readonly ?array $game,
        public readonly ?array $serie,
        public readonly ?array $platform,
    ) {
    }
}
