<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Chart;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Chart;
use App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\AbstractFormDataController;

/**
 * Call api for form submit scores
 * Return charts with the one relation player-chart of the connected user
 * If the user has not relation, a default relation is created
 */
class GetFormData extends AbstractFormDataController
{
    /**
     * @param Chart   $chart
     * @param Request $request
     * @return mixed
     * @throws ORMException
     */
    public function __invoke(Chart $chart, Request $request): mixed
    {
        $this->game = $chart->getGroup()->getGame();

        $player = $this->userProvider->getPlayer();
        $page = 1;
        $itemsPerPage = 20;
        $locale = $request->getLocale();
        $search = [
            'chart' => $chart,
        ];

        $charts = $this->em->getRepository(Chart::class)->getList(
            $player,
            $page,
            $search,
            $locale,
            $itemsPerPage
        );

        return $this->setScores(new ArrayCollection(iterator_to_array($charts)), $player);
    }
}
