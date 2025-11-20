<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\PlayerChart;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChart;

class GetLatestScoresDifferentGames extends AbstractController
{
    private EntityManagerInterface $em;
    private CacheInterface $cache;

    public function __construct(EntityManagerInterface $em, CacheInterface $cache)
    {
        $this->em = $em;
        $this->cache = $cache;
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(Request $request): array
    {
        $limit = $request->query->getInt('limit', 5);
        $days = $request->query->getInt('days', 30);

        $cacheKey = "latest_scores_different_games_{$limit}_{$days}";

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($limit, $days) {
            $item->expiresAfter(300); // 5 minutes cache

            $date = new \DateTime("-{$days} days");

            $sql = "
                SELECT pc1.*
                FROM vgr_player_chart pc1
                INNER JOIN vgr_chart c ON pc1.chart_id = c.id
                INNER JOIN vgr_group g ON c.group_id = g.id
                WHERE pc1.last_update >= :date
                  AND pc1.id > 0
                  AND NOT EXISTS (
                      SELECT 1
                      FROM vgr_player_chart pc2
                      INNER JOIN vgr_chart c2 ON pc2.chart_id = c2.id
                      INNER JOIN vgr_group g2 ON c2.group_id = g2.id
                      WHERE pc2.last_update > pc1.last_update
                        AND g2.game_id = g.game_id
                  )
                ORDER BY pc1.last_update DESC
                LIMIT :limit
            ";

            $stmt = $this->em->getConnection()->prepare($sql);
            $stmt->bindValue('date', $date->format('Y-m-d H:i:s'));
            $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);

            $results = $stmt->executeQuery()->fetchAllAssociative();

            return array_map(function ($row) {
                return $this->em->getRepository(PlayerChart::class)->find($row['id']);
            }, $results);
        });
    }
}
