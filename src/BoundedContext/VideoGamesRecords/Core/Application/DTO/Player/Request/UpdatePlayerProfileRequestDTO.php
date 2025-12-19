<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePlayerProfileRequestDTO
{
    public function __construct(
        #[Assert\Url(message: 'Website must be a valid URL')]
        public readonly ?string $website = null,
        #[Assert\Url(message: 'YouTube URL must be valid')]
        public readonly ?string $youtube = null,
        #[Assert\Url(message: 'Twitch URL must be valid')]
        public readonly ?string $twitch = null,
        #[Assert\Length(max: 50, maxMessage: 'Discord cannot exceed 50 characters')]
        public readonly ?string $discord = null,
        #[Assert\Length(max: 1000, maxMessage: 'Presentation cannot exceed 1000 characters')]
        public readonly ?string $presentation = null,
        #[Assert\Length(max: 500, maxMessage: 'Collection cannot exceed 500 characters')]
        public readonly ?string $collection = null,
        #[Assert\Date(message: 'Birth date must be a valid date')]
        public readonly ?string $birthDate = null,
        #[Assert\Type('integer', message: 'Country ID must be an integer')]
        #[Assert\Positive(message: 'Country ID must be positive')]
        public readonly ?int $countryId = null
    ) {
    }
}
