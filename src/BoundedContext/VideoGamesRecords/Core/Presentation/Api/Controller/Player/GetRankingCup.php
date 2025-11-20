<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Player;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Core\Application\DataProvider\Ranking\PlayerRankingProvider;

class GetRankingCup extends AbstractController
{
    private PlayerRankingProvider $playerRankingProvider;

    public function __construct(PlayerRankingProvider $playerRankingProvider)
    {
        $this->playerRankingProvider = $playerRankingProvider;
    }

     /**
     * @param Request $request
     * @return array<string, mixed>
     * @throws ORMException
     */
    public function __invoke(Request $request): array
    {
        return $this->playerRankingProvider->getRankingCup(
            [
                'maxRank' => $request->query->get('maxRank', '5'),
                'idTeam' => $request->query->get('idTeam'),
                'limit' => $request->query->get('limit')
            ]
        );
    }
}
