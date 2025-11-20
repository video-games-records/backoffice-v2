<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\EventSubscriber\Api;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\BoundedContext\VideoGamesRecords\Proof\Domain\Entity\ProofRequest;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Security\UserProvider;
use App\BoundedContext\VideoGamesRecords\Proof\Application\DataProvider\CanAskProofProvider;

final class CanAskProofSubscriber implements EventSubscriberInterface
{
    public const MAX_PROOF_REQUEST_DAY = 5;

    private TranslatorInterface $translator;
    private CanAskProofProvider $canAskProofProvider;
    private UserProvider $userProvider;

    public function __construct(
        TranslatorInterface $translator,
        CanAskProofProvider $canAskProofProvider,
        UserProvider $userProvider
    ) {
        $this->translator = $translator;
        $this->canAskProofProvider = $canAskProofProvider;
        $this->userProvider = $userProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['setPlayerRequesting', EventPriorities::POST_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     * @return void
     * @throws ORMException
     */
    public function setPlayerRequesting(ViewEvent $event): void
    {
        $request = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (($request instanceof ProofRequest) && ($method == Request::METHOD_POST)) {
            $player = $this->userProvider->getPlayer();

            if (false === $this->canAskProofProvider->load($player)) {
                throw new \Exception(
                    sprintf(
                        $this->translator->trans('proof.request.send.refuse'),
                        self::MAX_PROOF_REQUEST_DAY
                    )
                );
            }

            $request->setPlayerRequesting($player);
        }
    }
}
