<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Request\UpdatePlayerFriendsRequestDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\UpdatePlayerFriendsResponseDTO;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerRepository;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Security\UserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdatePlayerFriendsDataProvider implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PlayerRepository $playerRepository,
        private readonly UserProvider $userProvider
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): UpdatePlayerFriendsResponseDTO
    {
        /** @var UpdatePlayerFriendsRequestDTO $updateRequest */
        $updateRequest = $data;

        $playerId = $uriVariables['id'] ?? throw new BadRequestHttpException('Player ID is required');

        /** @var Player $player */
        $player = $this->playerRepository->find($playerId);
        if (!$player) {
            throw new NotFoundHttpException('Player not found');
        }

        // Vérifier que l'utilisateur ne peut modifier que ses propres amis
        $currentPlayer = $this->userProvider->getPlayer();
        if ($currentPlayer->getId() !== $player->getId()) {
            throw new AccessDeniedHttpException('You can only update your own friends list');
        }

        $friendsAdded = 0;
        $friendsRemoved = 0;

        // Ajouter des amis
        foreach ($updateRequest->friendsToAdd as $friendId) {
            $friend = $this->playerRepository->find($friendId);
            if ($friend) {
                $player->addFriend($friend);
                $friendsAdded++;
            }
        }

        // Supprimer des amis
        foreach ($updateRequest->friendsToRemove as $friendId) {
            $friend = $this->playerRepository->find($friendId);
            if ($friend) {
                $player->removeFriend($friend);
                $friendsRemoved++;
            }
        }

        $this->entityManager->flush();

        return new UpdatePlayerFriendsResponseDTO(
            success: true,
            message: 'Friends list updated successfully',
            friendsAdded: $friendsAdded,
            friendsRemoved: $friendsRemoved,
            totalFriends: $player->getFriends()->count()
        );
    }
}
