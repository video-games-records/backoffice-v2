<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Group;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Locale;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Chart;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Group;
use App\BoundedContext\VideoGamesRecords\Core\Domain\ValueObject\GroupOrderBy;

class GetCharts extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param Group $group
     * @param Request $request
     * @return array<string, mixed>
     */
    public function __invoke(Group $group, Request $request): array
    {
        $orderBy = ['id' => 'ASC'];
        if ($group->getOrderBy() === GroupOrderBy::NAME) {
            $locale = Locale::getDefault();
            if ($locale === 'fr') {
                $orderBy = ['libChartFr' => 'ASC'];
            } else {
                $orderBy = ['libChartEn' => 'ASC'];
            }
        } elseif ($group->getOrderBy() === GroupOrderBy::ID) {
            $orderBy = ['id' => 'ASC'];
        }
        return $this->em->getRepository(Chart::class)->findBy(
            ['group' => $group],
            $orderBy
        );
    }
}
