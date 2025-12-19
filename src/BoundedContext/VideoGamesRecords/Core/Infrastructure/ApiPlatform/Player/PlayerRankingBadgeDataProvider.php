<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DataProvider\Ranking\PlayerRankingProvider;
use App\BoundedContext\VideoGamesRecords\Core\Application\Mapper\PlayerMapper;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerRankingBadgeDTO;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\RequestStack;

class PlayerRankingBadgeDataProvider implements ProviderInterface
{
    public function __construct(
        private PlayerRankingProvider $playerRankingProvider,
        private PlayerMapper $playerMapper,
        private RequestStack $requestStack
    ) {
    }

    /**
     * @return array<PlayerRankingBadgeDTO>
     * @throws ORMException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $request = $this->requestStack->getCurrentRequest();

        $players = $this->playerRankingProvider->getRankingBadge([
            'maxRank' => $request?->query->get('maxRank', '5'),
            'idTeam' => $request?->query->get('idTeam'),
            'limit' => $request?->query->get('limit')
        ]);

        return array_map(
            fn($player) => $this->playerMapper->toPlayerRankingBadgeDTO($player),
            $players
        );
    }
}
