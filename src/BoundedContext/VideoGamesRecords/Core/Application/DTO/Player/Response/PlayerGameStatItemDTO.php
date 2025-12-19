<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Game\Response\GameMinimalDTO;

class PlayerGameStatItemDTO
{
    /**
     * @param array<int, array<string, mixed>> $statuses
     */
    public function __construct(
        public readonly GameMinimalDTO $game,
        public readonly int $nbChart,
        public readonly int $nbChartWithoutDlc,
        public readonly int $nbChartProven,
        public readonly int $nbChartProvenWithoutDlc,
        public readonly int $nbEqual,
        public readonly int $rankMedal,
        public readonly int $chartRank0,
        public readonly int $chartRank1,
        public readonly int $chartRank2,
        public readonly int $chartRank3,
        public readonly int $chartRank4,
        public readonly int $chartRank5,
        public readonly int $rankPointChart,
        public readonly int $pointChart,
        public readonly int $pointChartWithoutDlc,
        public readonly int $pointGame,
        public readonly \DateTimeInterface $lastUpdate,
        public readonly array $statuses,
    ) {
    }
}
