<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Proof\Presentation\Api\Controller\Video;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\BoundedContext\VideoGamesRecords\Proof\Domain\Entity\Video;
use App\BoundedContext\VideoGamesRecords\Core\Application\Service\VideoRecommendationService;

#[AsController]
class GetRelatedVideos extends AbstractController
{
    public function __construct(
        private readonly VideoRecommendationService $videoRecommendationService
    ) {
    }

    /**
     * @return array<mixed>
     */
    public function __invoke(Video $data, Request $request): array
    {
        $limit = min(20, max(1, (int) $request->query->get('limit', 10)));

        return $this->videoRecommendationService->getRelatedVideos($data, $limit);
    }
}
