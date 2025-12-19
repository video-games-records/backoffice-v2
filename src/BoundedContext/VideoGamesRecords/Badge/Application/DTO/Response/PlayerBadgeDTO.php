<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Badge\Application\DTO\Response;

class PlayerBadgeDTO
{
    public function __construct(
        public readonly int $id,
        public readonly BadgeDTO $badge,
        public readonly \DateTimeInterface $createdAt,
        public readonly ?\DateTimeInterface $endedAt,
        public readonly ?int $mbOrder,
    ) {
    }
}
