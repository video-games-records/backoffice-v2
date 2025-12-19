<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Team\Application\Mapper;

use App\BoundedContext\VideoGamesRecords\Team\Application\DTO\Response\TeamPlayersDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerBasicDTO;
use App\BoundedContext\VideoGamesRecords\Team\Domain\Entity\Team;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;

class TeamPlayersMapper
{
    public function toResponseDTO(Team $team): TeamPlayersDTO
    {
        $playerDTOs = [];

        /** @var Player $player */
        foreach ($team->getPlayers() as $player) {
            $playerDTOs[] = new PlayerBasicDTO(
                id: $player->getId() ?? 0,
                pseudo: $player->getPseudo(),
                slug: $player->getSlug()
            );
        }

        return new TeamPlayersDTO($playerDTOs);
    }
}
