<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Request\UpdatePlayerProfileRequestDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\UpdatePlayerProfilePostResponseDTO;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerRepository;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Security\UserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UpdatePlayerProfilePostProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PlayerRepository $playerRepository,
        private readonly UserProvider $userProvider
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): UpdatePlayerProfilePostResponseDTO
    {
        /** @var UpdatePlayerProfileRequestDTO $updateRequest */
        $updateRequest = $data;

        $playerId = $uriVariables['id'] ?? throw new BadRequestHttpException('Player ID is required');

        /** @var Player $player */
        $player = $this->playerRepository->find($playerId);
        if (!$player) {
            throw new NotFoundHttpException('Player not found');
        }

        // Vérifier que l'utilisateur ne peut modifier que son propre profil
        $currentPlayer = $this->userProvider->getPlayer();
        if ($currentPlayer->getId() !== $player->getId()) {
            throw new AccessDeniedHttpException('You can only update your own profile');
        }

        // Update only provided fields
        if ($updateRequest->website !== null) {
            $player->setWebsite($updateRequest->website);
        }

        if ($updateRequest->youtube !== null) {
            $player->setYoutube($updateRequest->youtube);
        }

        if ($updateRequest->twitch !== null) {
            $player->setTwitch($updateRequest->twitch);
        }

        if ($updateRequest->discord !== null) {
            $player->setDiscord($updateRequest->discord);
        }

        if ($updateRequest->presentation !== null) {
            $player->setPresentation($updateRequest->presentation);
        }

        if ($updateRequest->collection !== null) {
            $player->setCollection($updateRequest->collection);
        }

        if ($updateRequest->birthDate !== null) {
            $player->setBirthDate(new \DateTime($updateRequest->birthDate));
        }

        if ($updateRequest->countryId !== null) {
            // Tu devras récupérer l'entité Country depuis le repository
            // $country = $this->countryRepository->find($updateRequest->countryId);
            // $player->setCountry($country);
        }

        $this->entityManager->flush();

        return new UpdatePlayerProfilePostResponseDTO(
            success: true,
            message: 'Profile updated successfully',
            website: $player->getWebsite(),
            youtube: $player->getYoutube(),
            twitch: $player->getTwitch(),
            discord: $player->getDiscord(),
            presentation: $player->getPresentation(),
            collection: $player->getCollection(),
            birthDate: $player->getBirthDate()?->format('Y-m-d'),
            countryId: $player->getCountry()?->getId()
        );
    }
}
