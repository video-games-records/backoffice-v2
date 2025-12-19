<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePlayerFriendsRequestDTO
{
    /**
     * @param int[] $friendsToAdd Array of friend IDs to add
     * @param int[] $friendsToRemove Array of friend IDs to remove
     */
    public function __construct(
        #[Assert\Type('array')]
        #[Assert\All([
            new Assert\Type('integer'),
            new Assert\Positive()
        ])]
        public readonly array $friendsToAdd = [],
        #[Assert\Type('array')]
        #[Assert\All([
            new Assert\Type('integer'),
            new Assert\Positive()
        ])]
        public readonly array $friendsToRemove = []
    ) {
    }
}
