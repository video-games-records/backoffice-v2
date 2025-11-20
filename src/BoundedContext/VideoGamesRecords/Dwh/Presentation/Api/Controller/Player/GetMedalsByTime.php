<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Dwh\Presentation\Api\Controller\Player;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Dwh\Infrastructure\Doctrine\Repository\PlayerRepository;

class GetMedalsByTime extends AbstractController
{
    private PlayerRepository $playerRepository;

    public function __construct(PlayerRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    /**
     * @param Player $player
     * @return array
     */
    public function __invoke(Player $player): array
    {
        $list = $this->playerRepository->findBy(['id' => $player->getId()], ['date' => 'ASC']);

        $return = [
            'rank0' => [],
            'rank1' => [],
            'rank2' => [],
            'rank3' => [],
            'date' => [],
        ];
        foreach ($list as $object) {
            $return['rank0'][] = $object->getChartRank0();
            $return['rank1'][] = $object->getChartRank1();
            $return['rank2'][] = $object->getChartRank2();
            $return['rank3'][] = $object->getChartRank3();
            $return['date'][] = $object->getDate();
        }
        return $return;
    }
}
