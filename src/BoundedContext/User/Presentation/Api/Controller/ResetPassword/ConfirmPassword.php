<?php

namespace App\BoundedContext\User\Presentation\Api\Controller\ResetPassword;

use App\BoundedContext\User\Application\Service\SecurityHistoryManager;
use App\SharedKernel\Domain\Security\SecurityEventTypeEnum;
use App\BoundedContext\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ConfirmPassword extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SecurityHistoryManager $securityHistoryManager
    ) {
    }


    /**
     * @throws Exception
     */
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'];
        $plainPassword = $data['plainPassword'];

        $user = $this->em->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

        if (null === $user) {
            throw new BadRequestException();
        }

        $user->setPlainPassword($plainPassword);
        $user->setConfirmationToken(null);

        $this->em->flush();

        // Log security event
        $this->securityHistoryManager->recordEvent($user, SecurityEventTypeEnum::PASSWORD_RESET_COMPLETE, [
            'email' => $user->getEmail()
        ]);

        return new JsonResponse(['success' => true]);
    }
}
