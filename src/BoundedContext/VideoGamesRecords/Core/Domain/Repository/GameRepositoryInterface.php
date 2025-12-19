<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Domain\Repository;

use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game;

interface GameRepositoryInterface
{
    public function findById(int $id): ?Game;
}
