<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Player\Friend;

use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;

class GetFriends extends AbstractController
{
    public function __invoke(Player $player): Collection
    {
        return $player->getFriends();
    }
}
