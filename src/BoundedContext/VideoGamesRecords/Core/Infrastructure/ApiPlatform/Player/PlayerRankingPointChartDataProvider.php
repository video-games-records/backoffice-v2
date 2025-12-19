<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DataProvider\Ranking\PlayerRankingProvider;
use App\BoundedContext\VideoGamesRecords\Core\Application\Mapper\PlayerMapper;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerRankingPointChartDTO;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\RequestStack;

class PlayerRankingPointChartDataProvider implements ProviderInterface
{
    public function __construct(
        private PlayerRankingProvider $playerRankingProvider,
        private PlayerMapper $playerMapper,
        private RequestStack $requestStack
    ) {
    }

    /**
     * @return array<PlayerRankingPointChartDTO>
     * @throws ORMException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $request = $this->requestStack->getCurrentRequest();

        $players = $this->playerRankingProvider->getRankingPointChart([
            'maxRank' => $request?->query->get('maxRank', '5'),
            'idTeam' => $request?->query->get('idTeam'),
            'limit' => $request?->query->get('limit')
        ]);

        return array_map(
            fn($player) => $this->playerMapper->toPlayerRankingPointChartDTO($player),
            $players
        );
    }
}
