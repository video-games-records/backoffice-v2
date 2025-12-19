<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Request;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class AddFriendRequestDTO
{
    public function __construct(
        #[SerializedName('friend_id')]
        #[Assert\NotBlank(message: 'Friend ID is required')]
        #[Assert\Type(type: 'integer', message: 'Friend ID must be an integer')]
        #[Assert\Positive(message: 'Friend ID must be positive')]
        public readonly int $friendId
    ) {
    }
}
