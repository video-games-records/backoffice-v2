<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

class PlayerStatusDTO
{
    public function __construct(
        public readonly string $value,
        public readonly string $label,
        public readonly bool $isAdmin,
        public readonly bool $isModerator,
    ) {
    }
}
