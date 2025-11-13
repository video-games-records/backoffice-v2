<?php

namespace App\BoundedContext\User\Presentation\Api\Controller\ResetPassword;

use App\BoundedContext\User\Application\Service\SecurityHistoryManager;
use App\BoundedContext\User\Application\Service\UserManager;
use App\SharedKernel\Domain\Security\SecurityEventTypeEnum;
use App\SharedKernel\Infrastructure\TokenGenerator;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsController]
class SendPasswordResetLink extends AbstractController
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly TokenGenerator $tokenGenerator,
        private readonly MailerInterface $mailer,
        private readonly TranslatorInterface $translator,
        private readonly SecurityHistoryManager $securityHistoryManager,
        private readonly int $retryTtl = 7200
    ) {
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $callBackUrl = $data['callBackUrl'];

        $user = $this->userManager->findUserByUsernameOrEmail($email);
        if ($user && (null === $user->getPasswordRequestedAt() || $user->isPasswordRequestExpired($this->retryTtl))) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
            $body = sprintf(
                $this->translator->trans('password_reset.message', [], 'UserEmail', $user->getLanguage()),
                $user->getUsername(),
                ($request->server->get('HTTP_ORIGIN') ?? null) .
                str_replace('[token]', $user->getConfirmationToken(), $callBackUrl)
            );

            $email = (new Email())
                ->to($user->getEmail())
                ->subject($this->translator->trans('password_reset.subject', [], 'UserEmail', $user->getLanguage()))
                ->text($body)
                ->html($body);

            $this->mailer->send($email);

            $user->setPasswordRequestedAt(new DateTime());
            $this->userManager->updateUser($user);

            $this->securityHistoryManager->recordEvent($user, SecurityEventTypeEnum::PASSWORD_RESET_REQUEST, [
                'email' => $user->getEmail()
            ]);
        }

        return new JsonResponse(['success' => true]);
    }
}
