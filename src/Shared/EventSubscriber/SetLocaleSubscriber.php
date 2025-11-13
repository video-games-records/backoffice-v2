<?php

declare(strict_types=1);

namespace App\Shared\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SetLocaleSubscriber implements EventSubscriberInterface
{
    private string $defaultLocale = 'en';

    /** @var string[] */
    private array $supportedLocales = ['en', 'fr'];

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $pathInfo = $request->getPathInfo();

        if (str_starts_with($pathInfo, '/admin')) {
            \Locale::setDefault('en');
            $request->setLocale('en');
            return;
        }

        $locale = $this->getPreferredLocale($request);
        $validatedLocale = $this->validateLocale($locale);

        \Locale::setDefault($validatedLocale);
        $request->setLocale($validatedLocale);
    }

    private function getPreferredLocale(Request $request): string
    {
        // Priorité 1: Header Accept-Language
        if ($request->headers->has('Accept-Language')) {
            $acceptLanguage = $request->headers->get('Accept-Language');
            $locale = locale_accept_from_http($acceptLanguage);
            if ($locale !== false) {
                return substr($locale, 0, 2);
            }
        }

        return $this->defaultLocale;
    }

    private function validateLocale(string $locale): string
    {
        return in_array($locale, $this->supportedLocales, true) ? $locale : $this->defaultLocale;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
