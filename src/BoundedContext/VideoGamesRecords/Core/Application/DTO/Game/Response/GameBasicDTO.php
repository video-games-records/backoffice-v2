<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Game\Response;

class GameBasicDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }
}
