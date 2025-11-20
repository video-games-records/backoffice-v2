<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Group;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Core\Application\DataProvider\TopScoreProvider;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Group;

class GetTopScore extends AbstractController
{
    public function __construct(private readonly TopScoreProvider $topScoreProvider)
    {
    }

    /**
     * @param Group $group
     * @param Request $request
     * @return mixed
     * @throws ORMException
     */
    public function __invoke(Group $group, Request $request): mixed
    {
        return $this->topScoreProvider->load($group, $request->getLocale());
    }
}
