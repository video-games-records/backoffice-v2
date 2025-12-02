<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Group;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Chart;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Group;
use App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\AbstractFormDataController;

/**
 * Call api for form submit scores
 * Return charts with the one relation player-chart of the connected user
 * If the user has not relation, a default relation is created
 */
class GetFormData extends AbstractFormDataController
{
    /**
     * @param Group   $group
     * @param Request $request
     * @throws ORMException
     */
    public function __invoke(Group $group, Request $request): Collection
    {
        $this->game = $group->getGame();

        $player = $this->userProvider->getPlayer();
        $page = (int) $request->query->get('page', '1');
        $itemsPerPage = (int) $request->query->get('itemsPerPage', '20');
        $locale = $request->getLocale();
        $search = [
            'group' => $group,
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
