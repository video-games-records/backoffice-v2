<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Domain\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Doctrine\Repository\PlayerSerieRepository;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\ChartRank0Trait;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\ChartRank1Trait;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\ChartRank2Trait;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\ChartRank3Trait;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\ChartRank4Trait;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\ChartRank5Trait;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\NbChartProvenTrait;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\NbChartProvenWithoutDlcTrait;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\NbChartTrait;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\NbChartWithoutDlcTrait;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\NbGameTrait;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\PointChartTrait;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\RankMedalTrait;
use App\BoundedContext\VideoGamesRecords\Shared\Domain\Traits\Entity\RankPointChartTrait;

#[ORM\Table(name:'vgr_player_serie')]
#[ORM\Entity(repositoryClass: PlayerSerieRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['player-serie:read']]
        ),
        new Get(
            normalizationContext: ['groups' => ['player-serie:read']]
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['player' => 'exact', 'serie' => 'exact'])]
class PlayerSerie
{
    use RankMedalTrait;
    use ChartRank0Trait;
    use ChartRank1Trait;
    use ChartRank2Trait;
    use ChartRank3Trait;
    use ChartRank4Trait;
    use ChartRank5Trait;
    use RankPointChartTrait;
    use PointChartTrait;
    use NbChartTrait;
    use NbChartWithoutDlcTrait;
    use NbChartProvenTrait;
    use NbChartProvenWithoutDlcTrait;
    use NbGameTrait;

    #[ApiProperty(identifier: false)]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name:'player_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Player $player;

    #[ApiProperty(identifier: false)]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Serie::class)]
    #[ORM\JoinColumn(name:'serie_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Serie $serie;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $pointChartWithoutDlc;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $pointGame;

    public function setPointChartWithoutDlc(int $pointChartWithoutDlc): void
    {
        $this->pointChartWithoutDlc = $pointChartWithoutDlc;
    }

    public function getPointChartWithoutDlc(): int
    {
        return $this->pointChartWithoutDlc;
    }

    public function setPointGame(int $pointGame): void
    {
        $this->pointGame = $pointGame;
    }

    public function getPointGame(): int
    {
        return $this->pointGame;
    }

    public function setSerie(Serie $serie): void
    {
        $this->serie = $serie;
    }

    public function getSerie(): Serie
    {
        return $this->serie;
    }

    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getMedalsBackgroundColor(): string
    {
        $class = [
            0 => '',
            1 => 'bg-first',
            2 => 'bg-second',
            3 => 'bg-third',
        ];

        if ($this->getRankMedal() <= 3) {
            return sprintf('class="%s"', $class[$this->getRankMedal()]);
        }

        return '';
    }
}
