<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerNewLostPositionCountDTO;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\LostPositionRepository;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @implements ProviderInterface<PlayerNewLostPositionCountDTO>
 */
class PlayerNewLostPositionCountDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly LostPositionRepository $lostPositionRepository,
    ) {
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): PlayerNewLostPositionCountDTO
    {
        $playerId = (int) $uriVariables['id'];
        $player = $this->playerRepository->find($playerId);

        if (!$player) {
            throw new \InvalidArgumentException('Player not found');
        }

        // Logic from LostPositionManager::getNbNewLostPosition
        if ($player->getLastDisplayLostPosition() !== null) {
            $count = $this->lostPositionRepository->getNbNewLostPosition($player);
        } else {
            $count = $this->lostPositionRepository->getNbLostPosition($player);
        }

        return new PlayerNewLostPositionCountDTO(count: (int) $count);
    }
}
