<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\ChartType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChartTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. Score (+) -> mask 30~, tri DESC
        $scorePlus = new ChartType();
        $scorePlus->setId(1);
        $scorePlus->setName('Score (+)');
        $scorePlus->setMask('30~');
        $scorePlus->setOrderBy('DESC');
        $manager->persist($scorePlus);

        // 2. Temps (XXX:XX.XX) (-) -> mask "30~:|2~.|2~", tri ASC
        $time = new ChartType();
        $time->setId(2);
        $time->setName('Temps (XXX:XX.XX) (-)');
        $time->setMask('30~:|2~.|2~');
        $time->setOrderBy('ASC');
        $manager->persist($time);

        // 3. Score (-) -> mask 30~, tri ASC
        $scoreMinus = new ChartType();
        $scoreMinus->setId(3);
        $scoreMinus->setName('Score (-)');
        $scoreMinus->setMask('30~');
        $scoreMinus->setOrderBy('ASC');
        $manager->persist($scoreMinus);

        $manager->flush();
    }
}
