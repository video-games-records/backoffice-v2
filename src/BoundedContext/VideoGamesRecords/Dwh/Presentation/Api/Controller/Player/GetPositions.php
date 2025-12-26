<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Dwh\Presentation\Api\Controller\Player;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Dwh\Infrastructure\Doctrine\Repository\PlayerRepository;

class GetPositions extends AbstractController
{
    private PlayerRepository $playerRepository;

    public function __construct(PlayerRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    /**
     * @param Player $player
     * @return array<int>
     */
    public function __invoke(Player $player): array
    {
        $object = $this->playerRepository->findOneBy(['id' => $player->getId()], ['date' => 'DESC']);
        return [
            $object->getChartRank1(),
            $object->getChartRank2(),
            $object->getChartRank3(),
            $object->getChartRank(4),
            $object->getChartRank(5),
            $object->getChartRank(6),
            $object->getChartRank(7),
            $object->getChartRank(8),
            $object->getChartRank(9),
            $object->getChartRank(10),
            $object->getChartRank(11),
            $object->getChartRank(12),
            $object->getChartRank(13),
            $object->getChartRank(14),
            $object->getChartRank(15),
            $object->getChartRank(16),
            $object->getChartRank(17),
            $object->getChartRank(18),
            $object->getChartRank(19),
            $object->getChartRank(20),
            $object->getChartRank(21),
            $object->getChartRank(22),
            $object->getChartRank(23),
            $object->getChartRank(24),
            $object->getChartRank(25),
            $object->getChartRank(26),
            $object->getChartRank(27),
            $object->getChartRank(28),
            $object->getChartRank(29),
            $object->getChartRank(30),
        ];
    }
}
