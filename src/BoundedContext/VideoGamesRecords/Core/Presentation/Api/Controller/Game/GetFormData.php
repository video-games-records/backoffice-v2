<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Game;

use ApiPlatform\Doctrine\Orm\Paginator;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Chart;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game;
use App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\AbstractFormDataController;

class GetFormData extends AbstractFormDataController
{
    /**
     * @param Game   $game
     * @param Request $request
     * @throws ORMException
     */
    public function __invoke(Game $game, Request $request): Paginator
    {
        $this->game = $game;

        $player = $this->userProvider->getPlayer();
        $page = (int) $request->query->get('page', '1');
        $itemsPerPage = (int) $request->query->get('itemsPerPage', '20');
        $locale = $request->getLocale();
        $search = [
            'game' => $game,
            'term' => $request->query->get('term', null),
        ];

        $charts = $this->em->getRepository(Chart::class)->getList(
            $player,
            $page,
            $search,
            $locale,
            $itemsPerPage
        );

        return $this->setScores($charts, $player);
    }
}
