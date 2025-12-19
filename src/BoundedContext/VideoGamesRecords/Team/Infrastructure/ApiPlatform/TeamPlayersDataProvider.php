<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Team\Infrastructure\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Team\Application\DTO\Response\TeamPlayersDTO;
use App\BoundedContext\VideoGamesRecords\Team\Application\Mapper\TeamPlayersMapper;
use App\BoundedContext\VideoGamesRecords\Team\Infrastructure\Doctrine\Repository\TeamRepository;

/** @implements ProviderInterface<TeamPlayersDTO> */
class TeamPlayersDataProvider implements ProviderInterface
{
    public function __construct(
        private TeamRepository $teamRepository,
        private TeamPlayersMapper $teamPlayersMapper
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?TeamPlayersDTO
    {
        $teamId = $uriVariables['id'] ?? null;

        if ($teamId === null) {
            return null;
        }

        $team = $this->teamRepository->find((int) $teamId);

        if ($team === null) {
            return null;
        }

        assert($team instanceof \App\BoundedContext\VideoGamesRecords\Team\Domain\Entity\Team);

        return $this->teamPlayersMapper->toResponseDTO($team);
    }
}
