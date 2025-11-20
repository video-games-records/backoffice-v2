<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Group\Player;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Contracts\Ranking\RankingProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DataProvider\Ranking\PlayerGroupRankingProvider;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Group;

class GetRankingMedals extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(
        #[Autowire(service: PlayerGroupRankingProvider::class)]
        RankingProviderInterface $rankingProvider
    ) {
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @param Group   $group
     * @param Request $request
     * @return array<string, mixed>
     */
    public function __invoke(Group $group, Request $request): array
    {
        return $this->rankingProvider->getRankingMedals(
            $group->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '5'),
                'idTeam' => $request->query->get('idTeam'),
                'user' => $this->getUser()
            ]
        );
    }
}
