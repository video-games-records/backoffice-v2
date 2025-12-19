<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\Mapper\Player;

use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerFriendsDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerBasicDTO;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;

class PlayerFriendsMapper
{
    public function toResponseDTO(Player $player): PlayerFriendsDTO
    {
        $friendDTOs = [];

        /** @var Player $friend */
        foreach ($player->getFriends() as $friend) {
            $friendDTOs[] = new PlayerBasicDTO(
                id: $friend->getId() ?? 0,
                pseudo: $friend->getPseudo(),
                slug: $friend->getSlug()
            );
        }

        return new PlayerFriendsDTO($friendDTOs);
    }
}
