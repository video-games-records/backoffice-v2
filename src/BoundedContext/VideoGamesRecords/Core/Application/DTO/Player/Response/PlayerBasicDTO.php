<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

class PlayerBasicDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $pseudo,
        public readonly string $slug,
    ) {
    }
}
