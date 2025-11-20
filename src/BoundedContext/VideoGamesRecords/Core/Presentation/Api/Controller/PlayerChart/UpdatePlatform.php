<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\PlayerChart;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\Manager\ScoreManager;
use App\BoundedContext\VideoGamesRecords\Core\Application\Message\Player\UpdatePlayerGameRank;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Security\UserProvider;

class UpdatePlatform extends AbstractController
{
    public function __construct(
        private readonly ScoreManager $scoreManager,
        private readonly UserProvider $userProvider,
        private readonly MessageBusInterface $bus
    ) {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ORMException
     * @throws ExceptionInterface
     */
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $idGame = $data['idGame'];
        $idPlatform = $data['idPlatform'];

        $this->scoreManager->updatePlatform(
            $this->userProvider->getPlayer(),
            $idGame,
            $idPlatform
        );

        $this->bus->dispatch(new UpdatePlayerGameRank(
            $this->userProvider->getPlayer()->getId(),
            $idGame
        ));

        return new JsonResponse(['success' => true]);
    }
}
