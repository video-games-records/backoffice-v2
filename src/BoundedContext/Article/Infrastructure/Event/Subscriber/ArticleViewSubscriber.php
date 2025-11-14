<?php

declare(strict_types=1);

namespace App\BoundedContext\Article\Infrastructure\Event\Subscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities as EventPrioritiesAlias;
use App\BoundedContext\Article\Application\Service\ViewCounterService;
use App\BoundedContext\Article\Domain\Entity\Article;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class ArticleViewSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ViewCounterService $viewCounterService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onView', EventPrioritiesAlias::POST_READ],
        ];
    }

    public function onView(RequestEvent $event): void
    {
        $article = $event->getRequest()->attributes->get('data');
        $method = $event->getRequest()->getMethod();

        if ($article && ($article instanceof Article) && $method == Request::METHOD_GET) {
            $this->viewCounterService->incrementView($article);
        }
    }
}
