<?php

declare(strict_types=1);

namespace App\BoundedContext\Article\Application\Service;

use Symfony\Component\HttpFoundation\Request;

class LocaleResolver
{
    /** @var array<string> */
    private array $supportedLocales;
    private string $defaultLocale;

    /**
     * @param array<string> $supportedLocales
     */
    public function __construct(
        array $supportedLocales = ['en', 'fr'],
        string $defaultLocale = 'en'
    ) {
        $this->supportedLocales = $supportedLocales;
        $this->defaultLocale = $defaultLocale;
    }

    public function getPreferredLocale(Request $request): string
    {
        $acceptLanguage = $request->headers->get('Accept-Language');
        if (!$acceptLanguage) {
            return $this->defaultLocale;
        }

        $locale = substr($acceptLanguage, 0, 2);

        if (in_array($locale, $this->supportedLocales, true)) {
            return $locale;
        }
        return $this->defaultLocale;
    }

    /**
     * @return array<string>
     */
    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }

    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }
}
