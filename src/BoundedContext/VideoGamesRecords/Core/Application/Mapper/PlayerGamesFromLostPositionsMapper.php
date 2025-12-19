<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\Mapper;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Intl\Locale;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerGamesFromLostPositionsDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Game\Response\GameBasicDTO;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game;

class PlayerGamesFromLostPositionsMapper
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function toResponseDTO(Player $player): PlayerGamesFromLostPositionsDTO
    {
        $games = $this->getGamesFromLostPositions($player);

        $gamesDTOs = [];

        /** @var Game $game */
        foreach ($games as $game) {
            $gamesDTOs[] = new GameBasicDTO(
                id: $game->getId() ?? 0,
                name: $game->getName()
            );
        }

        return new PlayerGamesFromLostPositionsDTO($gamesDTOs);
    }

    /**
     * @param Player $player
     * @return array<Game>
     */
    private function getGamesFromLostPositions(Player $player): array
    {
        $query = $this->em->createQuery('
            SELECT DISTINCT g
            FROM App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game g
            INNER JOIN App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Group gr WITH gr.game = g
            INNER JOIN App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Chart c WITH c.group = gr
            INNER JOIN App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\LostPosition lp WITH lp.chart = c
            WHERE lp.player = :player
            ORDER BY g.libGameEn ASC
        ');

        $query->setParameter('player', $player);
        return $query->getResult();
    }
}
