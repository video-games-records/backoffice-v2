<?php

declare(strict_types=1);

namespace App\Tests\SharedKernel\Domain\Service;

class TestLocaleResolver
{
    private static ?string $forcedLocale = null;

    public static function forceLocale(string $locale): void
    {
        self::$forcedLocale = $locale;
    }

    public static function clearForcedLocale(): void
    {
        self::$forcedLocale = null;
    }

    public static function getForcedLocale(): ?string
    {
        return self::$forcedLocale;
    }
}
