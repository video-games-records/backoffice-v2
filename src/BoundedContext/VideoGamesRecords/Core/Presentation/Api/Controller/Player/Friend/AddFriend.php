<?php

// src/Controller/Player/Friend/AddFriend.php
declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\Player\Friend;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerRepository;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Security\UserProvider;

class AddFriend extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserProvider $userProvider,
        private readonly PlayerRepository $playerRepository
    ) {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ORMException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['friend_id'])) {
            return new JsonResponse(['error' => 'friend_id is required'], 400);
        }

        $friendId = (int) $data['friend_id'];
        /** @var Player $friend */
        $friend = $this->playerRepository->find($friendId);

        if (!$friend) {
            return new JsonResponse(['error' => 'Friend not found'], 404);
        }

        $player = $this->userProvider->getPlayer();
        $player->addFriend($friend);

        $this->em->flush();

        return new JsonResponse(['success' => true], 200);
    }
}
