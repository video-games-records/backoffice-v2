<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerChartStatsDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\Mapper\PlayerMapper;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @phpstan-ignore missingType.generics
 */
class PlayerChartStatsDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly PlayerMapper $playerMapper,
        private readonly EntityManagerInterface $em,
        private readonly RequestStack $requestStack
    ) {
    }

    /**
     * @return array<PlayerChartStatsDTO>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $player = $this->playerRepository->find($uriVariables['id']);

        if (!$player) {
            throw new NotFoundHttpException('Player not found');
        }

        // Récupérer le paramètre idGame depuis la requête
        $request = $this->requestStack->getCurrentRequest();
        $idGame = $request?->query->get('idGame');

        $qb = $this->em->createQueryBuilder()
            ->select('pc')
            ->from('App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChart', 'pc')
            ->where('pc.player = :player')
            ->setParameter('player', $player);

        if ($idGame !== null) {
            $qb->join('pc.chart', 'c')
                ->join('c.group', 'g')
                ->join('g.game', 'game')
                ->andWhere('game.id = :idGame')
                ->setParameter('idGame', (int) $idGame);
        }

        $playerCharts = $qb->getQuery()->getResult();

        // Grouper les résultats par statut et compter
        $stats = [];
        foreach ($playerCharts as $playerChart) {
            $statusValue = $playerChart->getStatus()->value;
            if (!isset($stats[$statusValue])) {
                $stats[$statusValue] = 0;
            }
            $stats[$statusValue]++;
        }

        // Convertir en format attendu par le mapper
        $result = [];
        foreach ($stats as $status => $nb) {
            $result[] = ['status' => $status, 'nb' => $nb];
        }

        return $this->playerMapper->toPlayerChartStatsDTOArray($result);
    }
}
