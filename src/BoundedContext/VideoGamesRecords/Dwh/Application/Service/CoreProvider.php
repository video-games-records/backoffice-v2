<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Dwh\Application\Service;

use DomainException;
use App\BoundedContext\VideoGamesRecords\Dwh\Domain\Contract\Strategy\CoreStrategyInterface;

class CoreProvider
{
    /** @var CoreStrategyInterface[] */
    private array $strategies = [];

    public function getStrategy(string $name): CoreStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($name)) {
                return $strategy;
            }
        }

        throw new DomainException(sprintf('Unable to find a strategy to type [%s]', $name));
    }

    public function addStrategy(CoreStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }
}
