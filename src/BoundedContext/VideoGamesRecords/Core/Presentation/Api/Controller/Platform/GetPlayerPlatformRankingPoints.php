<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Contracts\Ranking\RankingProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DataProvider\Ranking\PlayerPlatformRankingProvider;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Platform;

class GetPlayerPlatformRankingPoints extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(
        #[Autowire(service: PlayerPlatformRankingProvider::class)]
        RankingProviderInterface $rankingProvider
    ) {
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @param Platform $platform
     * @param Request  $request
     * @return array<string, mixed>
     */
    public function __invoke(Platform $platform, Request $request): array
    {
        return $this->rankingProvider->getRankingPoints(
            $platform->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '100'),
            ]
        );
    }
}
