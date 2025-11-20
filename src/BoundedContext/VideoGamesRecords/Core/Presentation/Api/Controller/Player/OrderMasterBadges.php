<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Player;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Badge\Infrastructure\Doctrine\Repository\PlayerBadgeRepository;

class OrderMasterBadges extends AbstractController
{
    public function __construct(
        private readonly PlayerBadgeRepository $playerBadgeRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(Player $player, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid JSON data'], 400);
        }

        foreach ($data as $item) {
            if (!isset($item['id']) || !isset($item['mbOrder'])) {
                return $this->json(['error' => 'Missing id or mbOrder in item'], 400);
            }

            $playerBadge = $this->playerBadgeRepository->find($item['id']);

            if (!$playerBadge) {
                return $this->json(['error' => 'PlayerBadge not found'], 404);
            }

            if ($playerBadge->getPlayer()->getId() !== $player->getId()) {
                return $this->json(['error' => 'PlayerBadge does not belong to this player'], 403);
            }

            $playerBadge->setMbOrder((int) $item['mbOrder']);
        }

        $this->entityManager->flush();

        return $this->json(['success' => true]);
    }
}
