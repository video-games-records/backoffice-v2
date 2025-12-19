<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Request\AddFriendRequestDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\AddFriendResponseDTO;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerRepository;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Security\UserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddFriendDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserProvider $userProvider,
        private readonly PlayerRepository $playerRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AddFriendResponseDTO
    {
        /** @var AddFriendRequestDTO $addFriendRequest */
        $addFriendRequest = $context['data'] ?? throw new BadRequestHttpException('Invalid request data');

        $friendId = $addFriendRequest->friendId;

        /** @var Player $friend */
        $friend = $this->playerRepository->find($friendId);
        if (!$friend) {
            throw new NotFoundHttpException('Friend not found');
        }

        $player = $this->userProvider->getPlayer();
        $player->addFriend($friend);

        $this->entityManager->flush();

        return new AddFriendResponseDTO(
            success: true,
            message: 'Friend added successfully'
        );
    }
}
