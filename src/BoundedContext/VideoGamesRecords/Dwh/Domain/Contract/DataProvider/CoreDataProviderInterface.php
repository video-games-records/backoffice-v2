<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Dwh\Domain\Contract\DataProvider;

use DateTime;

interface CoreDataProviderInterface
{
    public function getData(): array;

    public function getNbPostDay(DateTime $date1, DateTime $date2): array;
}
