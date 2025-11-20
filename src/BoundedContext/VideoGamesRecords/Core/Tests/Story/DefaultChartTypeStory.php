<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Tests\Story;

use App\BoundedContext\VideoGamesRecords\Core\Tests\Factory\ChartTypeFactory;
use Zenstruck\Foundry\Story;

final class DefaultChartTypeStory extends Story
{
    public function build(): void
    {
        // 1. Score (+) -> mask 30~, tri DESC
        ChartTypeFactory::new()
            ->withName('Score (+)')
            ->withMask('30~')
            ->orderDesc()
            ->create([
                'id' => 1,
            ]);

        // 2. Temps (XXX:XX.XX) (-) -> mask 30~:|2~.|2~, tri ASC
        ChartTypeFactory::new()
            ->withName('Temps (XXX:XX.XX) (-)')
            ->withMask('30~:|2~.|2~')
            ->orderAsc()
            ->create([
                'id' => 2,
            ]);

        // 3. Score (-) -> mask 30~, tri ASC
        ChartTypeFactory::new()
            ->withName('Score (-)')
            ->withMask('30~')
            ->orderAsc()
            ->create([
                'id' => 3,
            ]);
    }

    public static function scorePlus(): object
    {
        return ChartTypeFactory::find(['name' => 'Score (+)']);
    }

    public static function time(): object
    {
        return ChartTypeFactory::find(['name' => 'Temps (XXX:XX.XX) (-)']);
    }

    public static function scoreMinus(): object
    {
        return ChartTypeFactory::find(['name' => 'Score (-)']);
    }
}
