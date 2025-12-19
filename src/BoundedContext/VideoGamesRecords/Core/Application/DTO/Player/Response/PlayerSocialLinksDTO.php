<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response;

class PlayerSocialLinksDTO
{
    public function __construct(
        public readonly ?string $website,
        public readonly ?string $youtube,
        public readonly ?string $twitch,
        public readonly ?string $discord,
    ) {
    }
}
