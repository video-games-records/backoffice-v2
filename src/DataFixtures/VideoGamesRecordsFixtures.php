<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\BoundedContext\VideoGamesRecords\Core\Tests\Story\DefaultGameStory;
use App\BoundedContext\VideoGamesRecords\Core\Tests\Story\DefaultPlatformStory;
use App\BoundedContext\VideoGamesRecords\Core\Tests\Story\DefaultSerieStory;
use App\BoundedContext\VideoGamesRecords\Core\Tests\Story\DefaultChartTypeStory;
use App\BoundedContext\VideoGamesRecords\Core\Tests\Story\DefaultGroupStory;
use App\BoundedContext\VideoGamesRecords\Core\Tests\Story\DefaultChartStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VideoGamesRecordsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Charger d'abord les plateformes puis les séries, puis les jeux, groupes et charts du bounded context VideoGamesRecords
        DefaultPlatformStory::load();
        DefaultSerieStory::load();
        DefaultGameStory::load();
        DefaultChartTypeStory::load();
        DefaultGroupStory::load();
        DefaultChartStory::load();

        // Références utiles pour d'autres fixtures/tests
        $switch = DefaultPlatformStory::nintendoSwitch();
        $pc = DefaultPlatformStory::pc();
        $ps4 = DefaultPlatformStory::ps4();
        $xboxOne = DefaultPlatformStory::xboxOne();

        $mario = DefaultGameStory::mario();
        $zelda = DefaultGameStory::zelda();
        $hollowKnight = DefaultGameStory::hollowKnight();

        $serieMario = DefaultSerieStory::mario();
        $serieZelda = DefaultSerieStory::zelda();
        $serieMetroid = DefaultSerieStory::metroid();

        // Chart types
        $chartTypeScorePlus = DefaultChartTypeStory::scorePlus();
        $chartTypeTime = DefaultChartTypeStory::time();
        $chartTypeScoreMinus = DefaultChartTypeStory::scoreMinus();

        $this->addReference('platform_switch', $switch->_real());
        $this->addReference('platform_pc', $pc->_real());
        $this->addReference('platform_ps4', $ps4->_real());
        $this->addReference('platform_xbox_one', $xboxOne->_real());

        $this->addReference('game_mario_odyssey', $mario->_real());
        $this->addReference('game_zelda_botw', $zelda->_real());
        $this->addReference('game_hollow_knight', $hollowKnight->_real());

        $this->addReference('serie_mario', $serieMario->_real());
        $this->addReference('serie_zelda', $serieZelda->_real());
        $this->addReference('serie_metroid', $serieMetroid->_real());

        // Chart type references
        $this->addReference('chart_type_score_plus', $chartTypeScorePlus->_real());
        $this->addReference('chart_type_time', $chartTypeTime->_real());
        $this->addReference('chart_type_score_minus', $chartTypeScoreMinus->_real());

        // Group references
        $this->addReference('group_mario_main', DefaultGroupStory::marioMainGame()->_real());
        $this->addReference('group_mario_moon_rocks', DefaultGroupStory::marioMoonRocks()->_real());
        $this->addReference('group_zelda_main_quest', DefaultGroupStory::zeldaMainQuest()->_real());
        $this->addReference('group_zelda_side_quests', DefaultGroupStory::zeldaSideQuests()->_real());
        $this->addReference('group_zelda_master_trials', DefaultGroupStory::zeldaMasterTrials()->_real());
        $this->addReference('group_zelda_champions_ballad', DefaultGroupStory::zeldaChampionsBallad()->_real());
        $this->addReference('group_hollow_knight_base', DefaultGroupStory::hollowKnightBase()->_real());
        $this->addReference('group_hollow_knight_steel_soul', DefaultGroupStory::hollowKnightSteelSoul()->_real());

        // Chart references
        $this->addReference('chart_mario_any_percent', DefaultChartStory::marioAnyPercent()->_real());
        $this->addReference('chart_mario_100_percent', DefaultChartStory::mario100Percent()->_real());
        $this->addReference('chart_mario_most_moons', DefaultChartStory::marioMostMoons()->_real());
        $this->addReference('chart_zelda_any_percent', DefaultChartStory::zeldaAnyPercent()->_real());
        $this->addReference('chart_zelda_100_percent', DefaultChartStory::zelda100Percent()->_real());
        $this->addReference('chart_zelda_all_shrines', DefaultChartStory::zeldaAllShrines()->_real());
        $this->addReference('chart_hollow_knight_any_percent', DefaultChartStory::hollowKnightAnyPercent()->_real());
        $this->addReference('chart_hollow_knight_112_percent', DefaultChartStory::hollowKnight112Percent()->_real());
        $this->addReference('chart_hollow_knight_steel_soul_any', DefaultChartStory::hollowKnightSteelSoulAny()->_real());
    }
}
