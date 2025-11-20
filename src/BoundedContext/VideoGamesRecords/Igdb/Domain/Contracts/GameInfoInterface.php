<?php

namespace App\BoundedContext\VideoGamesRecords\Igdb\Domain\Contracts;

use Doctrine\Common\Collections\Collection;
use DateTime;

interface GameInfoInterface
{
    public function getName(): string;
    public function getSlug(): string;
    public function getGenres(): Collection;
    public function getReleaseDate(): ?DateTime;
    public function getSummary(): ?string;
    public function getStoryline(): ?string;
    public function getUrl(): ?string;
}
