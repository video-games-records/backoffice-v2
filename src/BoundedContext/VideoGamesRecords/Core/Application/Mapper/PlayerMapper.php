<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Application\Mapper;

use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Country;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Player;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Country\Response\CountryDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerResponseDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerRankingPointChartDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerRankingPointGameDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerRankingMedalDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerRankingCupDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerRankingBadgeDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerRankingProofDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerStatusDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerStatsDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerChartStatsDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Player\Response\PlayerSocialLinksDTO;
use App\BoundedContext\VideoGamesRecords\Team\Application\DTO\Response\TeamDTO;
use App\BoundedContext\VideoGamesRecords\Team\Domain\Entity\Team;

class PlayerMapper
{
    public function __construct(
        private readonly CountryMapper $countryMapper
    ) {
    }
    public function toPlayerResponseDTO(Player $player): PlayerResponseDTO
    {
        return new PlayerResponseDTO(
            id: $player->getId(),
            pseudo: $player->getPseudo(),
            slug: $player->getSlug(),
            nbConnexion: $player->getNbConnexion(),
            hasDonate: $player->getHasDonate(),
            status: $this->toPlayerStatusDTO($player),
            stats: $this->toPlayerStatsDTO($player),
            team: $player->getTeam() ? $this->toTeamDTO($player->getTeam()) : null,
            lastLogin: $player->getLastLogin(),
            createdAt: $player->getCreatedAt(),
            socialLinks: $this->toPlayerSocialLinksDTO($player),
            presentation: $player->getPresentation(),
            collection: $player->getCollection(),
            country: $player->getCountry() ? $this->countryMapper->toCountryDTO($player->getCountry()) : null,
            birthDate: $player->getBirthDate(),
        );
    }

    public function toPlayerRankingPointChartDTO(Player $player): PlayerRankingPointChartDTO
    {
        return new PlayerRankingPointChartDTO(
            id: $player->getId(),
            pseudo: $player->getPseudo(),
            slug: $player->getSlug(),
            rank: $player->getRankPointChart(),
            point: $player->getPointChart(),
            nbChart: $player->getNbChart(),
            team: $player->getTeam() ? $this->toTeamDTO($player->getTeam()) : null,
            country: $player->getCountry() ? $this->countryMapper->toCountryDTO($player->getCountry()) : null,
        );
    }

    public function toPlayerRankingPointGameDTO(Player $player): PlayerRankingPointGameDTO
    {
        return new PlayerRankingPointGameDTO(
            id: $player->getId(),
            pseudo: $player->getPseudo(),
            slug: $player->getSlug(),
            rank: $player->getRankPointGame(),
            point: $player->getPointGame(),
            nbGame: $player->getNbGame(),
            team: $player->getTeam() ? $this->toTeamDTO($player->getTeam()) : null,
            country: $player->getCountry() ? $this->countryMapper->toCountryDTO($player->getCountry()) : null,
        );
    }

    public function toPlayerRankingMedalDTO(Player $player): PlayerRankingMedalDTO
    {
        return new PlayerRankingMedalDTO(
            id: $player->getId(),
            pseudo: $player->getPseudo(),
            slug: $player->getSlug(),
            rank: $player->getRankMedal(),
            platine: $player->getChartRank0(),
            gold: $player->getChartRank1(),
            silver: $player->getChartRank2(),
            bronze: $player->getChartRank3(),
            team: $player->getTeam() ? $this->toTeamDTO($player->getTeam()) : null,
            country: $player->getCountry() ? $this->countryMapper->toCountryDTO($player->getCountry()) : null,
        );
    }

    public function toPlayerRankingCupDTO(Player $player): PlayerRankingCupDTO
    {
        return new PlayerRankingCupDTO(
            id: $player->getId(),
            pseudo: $player->getPseudo(),
            slug: $player->getSlug(),
            rank: $player->getRankCup(),
            platine: $player->getGameRank0(),
            gold: $player->getGameRank1(),
            silver: $player->getGameRank2(),
            bronze: $player->getGameRank3(),
            team: $player->getTeam() ? $this->toTeamDTO($player->getTeam()) : null,
            country: $player->getCountry() ? $this->countryMapper->toCountryDTO($player->getCountry()) : null,
        );
    }

    public function toPlayerRankingBadgeDTO(Player $player): PlayerRankingBadgeDTO
    {
        return new PlayerRankingBadgeDTO(
            id: $player->getId(),
            pseudo: $player->getPseudo(),
            slug: $player->getSlug(),
            rank: $player->getRankBadge(),
            point: $player->getPointBadge(),
            nbMasterBadge: $player->getNbMasterBadge(),
            team: $player->getTeam() ? $this->toTeamDTO($player->getTeam()) : null,
            country: $player->getCountry() ? $this->countryMapper->toCountryDTO($player->getCountry()) : null,
        );
    }

    public function toPlayerRankingProofDTO(Player $player): PlayerRankingProofDTO
    {
        return new PlayerRankingProofDTO(
            id: $player->getId(),
            pseudo: $player->getPseudo(),
            slug: $player->getSlug(),
            rank: $player->getRankProof(),
            nbChart: $player->getNbChart(),
            nbChartProven: $player->getNbChartProven(),
            team: $player->getTeam() ? $this->toTeamDTO($player->getTeam()) : null,
            country: $player->getCountry() ? $this->countryMapper->toCountryDTO($player->getCountry()) : null,
        );
    }

    /**
     * @param array<array{status: string, nb: int}> $chartStats
     * @return array<PlayerChartStatsDTO>
     */
    public function toPlayerChartStatsDTOArray(array $chartStats): array
    {
        return array_map(
            fn(array $stat) => new PlayerChartStatsDTO(
                status: (string) $stat['status'],
                nb: (int) $stat['nb']
            ),
            $chartStats
        );
    }

    private function toPlayerStatusDTO(Player $player): PlayerStatusDTO
    {
        $status = $player->getStatus();

        return new PlayerStatusDTO(
            value: $status->value,
            label: $status->getLabel(),
            isAdmin: $status->isAdmin(),
            isModerator: $status->isModerator()
        );
    }

    private function toPlayerStatsDTO(Player $player): PlayerStatsDTO
    {
        return new PlayerStatsDTO(
            pointGame: $player->getPointGame(),
            pointChart: $player->getPointChart(),
            pointBadge: $player->getPointBadge(),
            nbGame: $player->getNbGame(),
            nbChart: $player->getNbChart(),
            nbVideo: $player->getNbVideo(),
            nbMasterBadge: $player->getNbMasterBadge(),
            nbChartProven: $player->getNbChartProven(),
            nbChartMax: $player->getNbChartMax(),
            chartRank0: $player->getChartRank0(),
            chartRank1: $player->getChartRank1(),
            chartRank2: $player->getChartRank2(),
            chartRank3: $player->getChartRank3(),
            chartRank4: $player->getChartRank4(),
            chartRank5: $player->getChartRank5(),
            gameRank0: $player->getGameRank0(),
            gameRank1: $player->getGameRank1(),
            gameRank2: $player->getGameRank2(),
            gameRank3: $player->getGameRank3(),
            rankCup: $player->getRankCup(),
            rankMedal: $player->getRankMedal(),
            rankBadge: $player->getRankBadge(),
            rankPointChart: $player->getRankPointChart(),
            rankPointGame: $player->getRankPointGame(),
            rankCountry: $player->getRankCountry(),
            rankProof: $player->getRankProof(),
            averageChartRank: $player->getAverageChartRank(),
            averageGameRank: $player->getAverageGameRank(),
        );
    }

    private function toTeamDTO(Team $team): TeamDTO
    {
        return new TeamDTO(
            id: $team->getId(),
            name: $team->getLibTeam(),
            tag: $team->getTag(),
            slug: $team->getSlug()
        );
    }

    private function toPlayerSocialLinksDTO(Player $player): PlayerSocialLinksDTO
    {
        return new PlayerSocialLinksDTO(
            website: $player->getWebsite(),
            youtube: $player->getYoutube(),
            twitch: $player->getTwitch(),
            discord: $player->getDiscord(),
        );
    }
}
