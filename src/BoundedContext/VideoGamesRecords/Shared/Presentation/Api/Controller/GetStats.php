<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Shared\Presentation\Api\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\GameRepository;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerRepository;

class GetStats extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(): array
    {
        /** @var PlayerRepository $playerRepository */
        $playerRepository = $this->em->getRepository('App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player');

        /** @var GameRepository $gameRepository */
        $gameRepository = $this->em->getRepository('App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game');

        $playerStats = $playerRepository->getStats();
        $gameStats = $gameRepository->getStats();

        return [
            'nbPlayer' => (int) $playerStats[1],
            'nbChart' => (int) $playerStats[2],
            'nbChartProven' => (int) $playerStats[3],
            'nbGame' => (int) $gameStats[1],
        ];
    }
}
