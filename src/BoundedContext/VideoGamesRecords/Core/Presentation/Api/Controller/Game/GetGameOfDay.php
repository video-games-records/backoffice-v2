<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Game;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Core\Application\Manager\GameOfDayManager;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game;

class GetGameOfDay extends AbstractController
{
    public function __construct(private readonly GameOfDayManager $gameOfDayManager)
    {
    }

    public function __invoke(Request $request): Game
    {
        if ($request->query->has('refresh')) {
            $game = $this->gameOfDayManager->regenerateGameOfDay();
        } else {
            $game = $this->gameOfDayManager->getGameOfDay();
        }

        return $game;
    }
}
