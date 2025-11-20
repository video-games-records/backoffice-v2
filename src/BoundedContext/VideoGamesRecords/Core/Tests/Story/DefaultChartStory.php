<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Tests\Story;

use App\BoundedContext\VideoGamesRecords\Core\Tests\Factory\ChartFactory;
use App\BoundedContext\VideoGamesRecords\Core\Tests\Story\DefaultGroupStory;
use Zenstruck\Foundry\Story;

final class DefaultChartStory extends Story
{
    public function build(): void
    {
        // S'assurer que les groupes par défaut existent
        DefaultGroupStory::load();

        $marioMainGame = DefaultGroupStory::marioMainGame();
        $marioMoonRocks = DefaultGroupStory::marioMoonRocks();
        $zeldaMainQuest = DefaultGroupStory::zeldaMainQuest();
        $zeldaSideQuests = DefaultGroupStory::zeldaSideQuests();
        $zeldaMasterTrials = DefaultGroupStory::zeldaMasterTrials();
        $zeldaChampionsBallad = DefaultGroupStory::zeldaChampionsBallad();
        $hollowKnightBase = DefaultGroupStory::hollowKnightBase();
        $hollowKnightSteelSoul = DefaultGroupStory::hollowKnightSteelSoul();

        // Charts pour Super Mario Odyssey - Main Game
        ChartFactory::new()
            ->withNames('Any% Speedrun', 'Speedrun Any%')
            ->forGroup($marioMainGame)
            ->videoProofOnly()
            ->create([
                'id' => 1,
            ]);

        ChartFactory::new()
            ->withNames('100% Speedrun', 'Speedrun 100%')
            ->forGroup($marioMainGame)
            ->videoProofOnly()
            ->create([
                'id' => 2,
            ]);

        ChartFactory::new()
            ->withNames('Most Moons', 'Plus de Lunes')
            ->forGroup($marioMainGame)
            ->pictureProofAllowed()
            ->create([
                'id' => 3,
            ]);

        // Charts pour Super Mario Odyssey - Moon Rocks (DLC)
        ChartFactory::new()
            ->withNames('Moon Rock Collection', 'Collection de Roches Lunaires')
            ->forGroup($marioMoonRocks)
            ->dlc()
            ->pictureProofAllowed()
            ->create([
                'id' => 4,
            ]);

        // Charts pour Zelda BOTW - Main Quest
        ChartFactory::new()
            ->withNames('Any% Speedrun', 'Speedrun Any%')
            ->forGroup($zeldaMainQuest)
            ->videoProofOnly()
            ->create([
                'id' => 5,
            ]);

        ChartFactory::new()
            ->withNames('100% Speedrun', 'Speedrun 100%')
            ->forGroup($zeldaMainQuest)
            ->videoProofOnly()
            ->create([
                'id' => 6,
            ]);

        ChartFactory::new()
            ->withNames('All Shrines', 'Tous les Sanctuaires')
            ->forGroup($zeldaMainQuest)
            ->pictureProofAllowed()
            ->create([
                'id' => 7,
            ]);

        // Charts pour Zelda BOTW - Side Quests
        ChartFactory::new()
            ->withNames('All Side Quests', 'Toutes les Quêtes Secondaires')
            ->forGroup($zeldaSideQuests)
            ->pictureProofAllowed()
            ->create([
                'id' => 8,
            ]);

        ChartFactory::new()
            ->withNames('Korok Seeds', 'Graines de Korogu')
            ->forGroup($zeldaSideQuests)
            ->pictureProofAllowed()
            ->create([
                'id' => 9,
            ]);

        // Charts pour Zelda BOTW - Master Trials (DLC)
        ChartFactory::new()
            ->withNames('Trial of the Sword', 'Épreuve de l\'Épée')
            ->forGroup($zeldaMasterTrials)
            ->dlc()
            ->videoProofOnly()
            ->create([
                'id' => 10,
            ]);

        // Charts pour Zelda BOTW - Champions' Ballad (DLC)
        ChartFactory::new()
            ->withNames('Champion\'s Ballad Speedrun', 'Speedrun Ode aux Prodiges')
            ->forGroup($zeldaChampionsBallad)
            ->dlc()
            ->videoProofOnly()
            ->create([
                'id' => 11,
            ]);

        // Charts pour Hollow Knight - Base Game
        ChartFactory::new()
            ->withNames('Any% Speedrun', 'Speedrun Any%')
            ->forGroup($hollowKnightBase)
            ->videoProofOnly()
            ->create([
                'id' => 12,
            ]);

        ChartFactory::new()
            ->withNames('112% Speedrun', 'Speedrun 112%')
            ->forGroup($hollowKnightBase)
            ->videoProofOnly()
            ->create([
                'id' => 13,
            ]);

        ChartFactory::new()
            ->withNames('All Bosses', 'Tous les Boss')
            ->forGroup($hollowKnightBase)
            ->videoProofOnly()
            ->create([
                'id' => 14,
            ]);

        ChartFactory::new()
            ->withNames('Completion Percentage', 'Pourcentage de Completion')
            ->forGroup($hollowKnightBase)
            ->pictureProofAllowed()
            ->create([
                'id' => 15,
            ]);

        // Charts pour Hollow Knight - Steel Soul Mode
        ChartFactory::new()
            ->withNames('Steel Soul Any%', 'Âme d\'Acier Any%')
            ->forGroup($hollowKnightSteelSoul)
            ->videoProofOnly()
            ->create([
                'id' => 16,
            ]);

        ChartFactory::new()
            ->withNames('Steel Soul 100%', 'Âme d\'Acier 100%')
            ->forGroup($hollowKnightSteelSoul)
            ->videoProofOnly()
            ->create([
                'id' => 17,
            ]);

        // Quelques charts supplémentaires aléatoires
        ChartFactory::new()
            ->many(5)
            ->create();
    }

    // Super Mario Odyssey charts
    public static function marioAnyPercent(): object
    {
        return ChartFactory::find(['libChartEn' => 'Any% Speedrun', 'group' => DefaultGroupStory::marioMainGame()]);
    }

    public static function mario100Percent(): object
    {
        return ChartFactory::find(['libChartEn' => '100% Speedrun', 'group' => DefaultGroupStory::marioMainGame()]);
    }

    public static function marioMostMoons(): object
    {
        return ChartFactory::find(['libChartEn' => 'Most Moons']);
    }

    public static function marioMoonRockCollection(): object
    {
        return ChartFactory::find(['libChartEn' => 'Moon Rock Collection']);
    }

    // Zelda BOTW charts
    public static function zeldaAnyPercent(): object
    {
        return ChartFactory::find(['libChartEn' => 'Any% Speedrun', 'group' => DefaultGroupStory::zeldaMainQuest()]);
    }

    public static function zelda100Percent(): object
    {
        return ChartFactory::find(['libChartEn' => '100% Speedrun', 'group' => DefaultGroupStory::zeldaMainQuest()]);
    }

    public static function zeldaAllShrines(): object
    {
        return ChartFactory::find(['libChartEn' => 'All Shrines']);
    }

    public static function zeldaSideQuests(): object
    {
        return ChartFactory::find(['libChartEn' => 'All Side Quests']);
    }

    public static function zeldaKorokSeeds(): object
    {
        return ChartFactory::find(['libChartEn' => 'Korok Seeds']);
    }

    public static function zeldaTrialOfSword(): object
    {
        return ChartFactory::find(['libChartEn' => 'Trial of the Sword']);
    }

    public static function zeldaChampionsBallad(): object
    {
        return ChartFactory::find(['libChartEn' => 'Champion\'s Ballad Speedrun']);
    }

    // Hollow Knight charts
    public static function hollowKnightAnyPercent(): object
    {
        return ChartFactory::find(['libChartEn' => 'Any% Speedrun', 'group' => DefaultGroupStory::hollowKnightBase()]);
    }

    public static function hollowKnight112Percent(): object
    {
        return ChartFactory::find(['libChartEn' => '112% Speedrun']);
    }

    public static function hollowKnightAllBosses(): object
    {
        return ChartFactory::find(['libChartEn' => 'All Bosses']);
    }

    public static function hollowKnightCompletion(): object
    {
        return ChartFactory::find(['libChartEn' => 'Completion Percentage']);
    }

    public static function hollowKnightSteelSoulAny(): object
    {
        return ChartFactory::find(['libChartEn' => 'Steel Soul Any%']);
    }

    public static function hollowKnightSteelSoul100(): object
    {
        return ChartFactory::find(['libChartEn' => 'Steel Soul 100%']);
    }
}
