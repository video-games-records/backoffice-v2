<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Tests\Story;

use App\BoundedContext\VideoGamesRecords\Core\Tests\Factory\PlayerFactory;
use App\BoundedContext\VideoGamesRecords\Core\Tests\Factory\PlayerStatusFactory;
use Zenstruck\Foundry\Story;

final class DefaultPlayerStory extends Story
{
    public function build(): void
    {
        // Créer les statuts de joueur par défaut
        $activeStatus = PlayerStatusFactory::new()->active()->create(['id' => 1]);
        $bannedStatus = PlayerStatusFactory::new()->banned()->create(['id' => 2]);
        $inactiveStatus = PlayerStatusFactory::new()->inactive()->create(['id' => 3]);

        // Créer quelques joueurs de référence
        PlayerFactory::new()
            ->topPlayer()
            ->withPseudo('TopPlayer')
            ->withUserId(1)
            ->create([
                'id' => 1,
                'status' => $activeStatus,
            ]);

        PlayerFactory::new()
            ->active()
            ->withPseudo('ActiveGamer')
            ->withUserId(2)
            ->create([
                'id' => 2,
                'status' => $activeStatus,
            ]);

        PlayerFactory::new()
            ->newPlayer()
            ->withPseudo('NewbieMaster')
            ->withUserId(3)
            ->create([
                'id' => 3,
                'status' => $activeStatus,
            ]);

        PlayerFactory::new()
            ->donor()
            ->withPseudo('GenerousDonor')
            ->withUserId(4)
            ->create([
                'id' => 4,
                'status' => $activeStatus,
            ]);

        PlayerFactory::new()
            ->withPseudo('BannedUser')
            ->withUserId(5)
            ->create([
                'id' => 5,
                'status' => $bannedStatus,
            ]);

        PlayerFactory::new()
            ->withPseudo('InactiveUser')
            ->withUserId(6)
            ->create([
                'id' => 6,
                'status' => $inactiveStatus,
            ]);

        // Créer quelques joueurs supplémentaires aléatoires
        PlayerFactory::new()
            ->many(5)
            ->create([
                'status' => $activeStatus,
            ]);
    }

    public static function topPlayer(): object
    {
        return PlayerFactory::find(['pseudo' => 'TopPlayer']);
    }

    public static function activeGamer(): object
    {
        return PlayerFactory::find(['pseudo' => 'ActiveGamer']);
    }

    public static function newbie(): object
    {
        return PlayerFactory::find(['pseudo' => 'NewbieMaster']);
    }

    public static function donor(): object
    {
        return PlayerFactory::find(['pseudo' => 'GenerousDonor']);
    }

    public static function bannedUser(): object
    {
        return PlayerFactory::find(['pseudo' => 'BannedUser']);
    }

    public static function inactiveUser(): object
    {
        return PlayerFactory::find(['pseudo' => 'InactiveUser']);
    }

    public static function activeStatus(): object
    {
        return PlayerStatusFactory::find(['class' => 'active']);
    }

    public static function bannedStatus(): object
    {
        return PlayerStatusFactory::find(['class' => 'banned']);
    }

    public static function inactiveStatus(): object
    {
        return PlayerStatusFactory::find(['class' => 'inactive']);
    }
}
