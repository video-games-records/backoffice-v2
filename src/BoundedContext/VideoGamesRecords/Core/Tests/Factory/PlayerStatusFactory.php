<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Tests\Factory;

use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerStatus;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerStatusTranslation;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PlayerStatus>
 */
final class PlayerStatusFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return PlayerStatus::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    protected function defaults(): array|callable
    {
        return [
            'class' => self::faker()->word(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (PlayerStatus $playerStatus): void {
                // Add English translation
                $translationEn = new PlayerStatusTranslation();
                $translationEn->setLocale('en');
                $translationEn->setName(self::faker()->words(2, true));
                $playerStatus->addTranslation($translationEn);

                // Add French translation
                $translationFr = new PlayerStatusTranslation();
                $translationFr->setLocale('fr');
                $translationFr->setName(self::faker()->words(2, true));
                $playerStatus->addTranslation($translationFr);
            });
    }

    /**
     * Create a default active status
     */
    public function active(): static
    {
        return $this->with(['class' => 'active'])
            ->afterInstantiate(function (PlayerStatus $playerStatus): void {
                $playerStatus->setName('Active', 'en');
                $playerStatus->setName('Actif', 'fr');
            });
    }

    /**
     * Create a banned status
     */
    public function banned(): static
    {
        return $this->with(['class' => 'banned'])
            ->afterInstantiate(function (PlayerStatus $playerStatus): void {
                $playerStatus->setName('Banned', 'en');
                $playerStatus->setName('Banni', 'fr');
            });
    }

    /**
     * Create an inactive status
     */
    public function inactive(): static
    {
        return $this->with(['class' => 'inactive'])
            ->afterInstantiate(function (PlayerStatus $playerStatus): void {
                $playerStatus->setName('Inactive', 'en');
                $playerStatus->setName('Inactif', 'fr');
            });
    }

    /**
     * Override class
     */
    public function withClass(string $class): static
    {
        return $this->with(['class' => $class]);
    }

    /**
     * Set custom translations
     */
    /**
     * @param array<string, string> $translations
     */
    public function withTranslations(array $translations): static
    {
        return $this->afterInstantiate(function (PlayerStatus $playerStatus) use ($translations): void {
            foreach ($translations as $locale => $name) {
                $playerStatus->setName($name, $locale);
            }
        });
    }
}
