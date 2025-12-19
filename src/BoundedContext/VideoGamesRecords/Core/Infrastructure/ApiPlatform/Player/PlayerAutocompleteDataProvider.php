<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerAutocompleteDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerBasicDTO;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerRepository;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @implements ProviderInterface<PlayerAutocompleteDTO>
 */
class PlayerAutocompleteDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): PlayerAutocompleteDTO
    {
        $request = $this->requestStack->getCurrentRequest();
        $query = $request?->query->get('query', '');

        if (empty($query)) {
            return new PlayerAutocompleteDTO([]);
        }

        $players = $this->playerRepository->autocomplete($query);

        $playerDTOs = array_map(
            fn($player) => new PlayerBasicDTO(
                id: $player->getId(),
                pseudo: $player->getPseudo(),
                slug: $player->getSlug(),
            ),
            $players
        );

        return new PlayerAutocompleteDTO($playerDTOs);
    }
}
