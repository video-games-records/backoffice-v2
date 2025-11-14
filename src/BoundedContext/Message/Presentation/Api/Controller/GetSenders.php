<?php

declare(strict_types=1);

namespace App\BoundedContext\Message\Presentation\Api\Controller;

use App\BoundedContext\Message\Infrastructure\Doctrine\Repository\MessageRepository;
use App\BoundedContext\User\Domain\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GetSenders extends AbstractController
{
    private MessageRepository $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    #[IsGranted('ROLE_USER')]
    public function __invoke(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException('User must be logged in.');
        }
        $senders = $this->messageRepository->getSenders($user);

        return new JsonResponse($senders);
    }
}
