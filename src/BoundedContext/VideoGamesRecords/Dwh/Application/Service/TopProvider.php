<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Dwh\Application\Service;

use DomainException;
use App\BoundedContext\VideoGamesRecords\Dwh\Domain\Contract\Strategy\TopStrategyInterface;

class TopProvider
{
    /** @var TopStrategyInterface[] */
    private array $strategies = [];

    public function getStrategy(string $name): TopStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($name)) {
                return $strategy;
            }
        }

        throw new DomainException(sprintf('Unable to find a strategy to type [%s]', $name));
    }

    public function addStrategy(TopStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }
}
