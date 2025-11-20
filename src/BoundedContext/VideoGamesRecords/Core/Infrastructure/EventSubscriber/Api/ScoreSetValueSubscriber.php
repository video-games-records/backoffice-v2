<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\EventSubscriber\Api;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChart;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChartLib;

final class ScoreSetValueSubscriber implements EventSubscriberInterface
{
    public function __construct()
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['setValue', EventPriorities::POST_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     */
    public function setValue(ViewEvent $event): void
    {
        $playerChart = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (
            ($playerChart instanceof PlayerChart)
            && in_array($method, [Request::METHOD_POST, Request::METHOD_PUT])
        ) {
            /** @var PlayerChartLib $lib */
            foreach ($playerChart->getLibs() as $lib) {
                $lib->setValueFromPaseValue();
            }
        }
    }
}
