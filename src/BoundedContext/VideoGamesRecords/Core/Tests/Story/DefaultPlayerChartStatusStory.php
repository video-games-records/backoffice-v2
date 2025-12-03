<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Tests\Story;

use App\BoundedContext\VideoGamesRecords\Core\Tests\Factory\PlayerChartStatusFactory;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChartStatus;
use Zenstruck\Foundry\Story;

final class DefaultPlayerChartStatusStory extends Story
{
    public function build(): void
    {
        // 1. none (normal status)
        $this->addState('none', PlayerChartStatusFactory::findOrCreate([
            'id' => PlayerChartStatus::ID_STATUS_NORMAL,
        ], [
            'class' => 'none',
            'boolRanking' => true,
        ]));

        // 2. request-pending (demand status)
        $this->addState('requestPending', PlayerChartStatusFactory::findOrCreate([
            'id' => PlayerChartStatus::ID_STATUS_DEMAND,
        ], [
            'class' => 'request-pending',
            'boolRanking' => true,
        ]));

        // 3. request-validated (investigation status)
        $this->addState('requestValidated', PlayerChartStatusFactory::findOrCreate([
            'id' => PlayerChartStatus::ID_STATUS_INVESTIGATION,
        ], [
            'class' => 'request-validated',
            'boolRanking' => true,
        ]));

        // 4. request-proof-sent (demand send proof status)
        $this->addState('requestProofSent', PlayerChartStatusFactory::findOrCreate([
            'id' => PlayerChartStatus::ID_STATUS_DEMAND_SEND_PROOF,
        ], [
            'class' => 'request-proof-sent',
            'boolRanking' => true,
        ]));

        // 5. proof-sent (normal send proof status)
        $this->addState('proofSent', PlayerChartStatusFactory::findOrCreate([
            'id' => PlayerChartStatus::ID_STATUS_NORMAL_SEND_PROOF,
        ], [
            'class' => 'proof-sent',
            'boolRanking' => true,
        ]));

        // 6. proved (proved status)
        $this->addState('proved', PlayerChartStatusFactory::findOrCreate([
            'id' => PlayerChartStatus::ID_STATUS_PROOVED,
        ], [
            'class' => 'proved',
            'boolRanking' => true,
        ]));

        // 7. unproved (not proved status - no ranking)
        $this->addState('unproved', PlayerChartStatusFactory::findOrCreate([
            'id' => PlayerChartStatus::ID_STATUS_NOT_PROOVED,
        ], [
            'class' => 'unproved',
            'boolRanking' => false,
        ]));
    }

    public static function none(): object
    {
        return static::get('none');
    }

    public static function requestPending(): object
    {
        return static::get('requestPending');
    }

    public static function requestValidated(): object
    {
        return static::get('requestValidated');
    }

    public static function requestProofSent(): object
    {
        return static::get('requestProofSent');
    }

    public static function proofSent(): object
    {
        return static::get('proofSent');
    }

    public static function proved(): object
    {
        return static::get('proved');
    }

    public static function unproved(): object
    {
        return static::get('unproved');
    }
}