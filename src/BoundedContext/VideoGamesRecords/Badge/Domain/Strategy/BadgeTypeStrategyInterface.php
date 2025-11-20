<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Badge\Domain\Strategy;

use App\BoundedContext\VideoGamesRecords\Badge\Domain\Contracts\BadgeInterface;
use App\BoundedContext\VideoGamesRecords\Badge\Domain\Entity\Badge;

interface BadgeTypeStrategyInterface extends BadgeInterface
{
    public function supports(Badge $badge): bool;

    public function getTitle(Badge $badge): string;
}
