<?php

namespace App\BoundedContext\User\Infrastructure\Event\Subscriber;

use App\BoundedContext\User\Domain\Entity\RefreshToken;
use App\BoundedContext\User\Domain\Event\PasswordChangedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class PasswordChangeSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator,
        private readonly RefreshTokenManagerInterface $refreshTokenManager,
    ) {
    }

    /**
     * @return array<string, array<int, array<int, int|string>>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PasswordChangedEvent::class => [
                ['notifyUser', 80], // Lower priority than basic logging
                ['enforceSecurityMeasures', 70],
            ],
        ];
    }

    public function notifyUser(PasswordChangedEvent $event): void
    {
        $user = $event->getUser();

        $this->logger->debug('Sending password change notification', [
            'user_id' => $user->getId(),
            'username' => $user->getUsername()
        ]);

        try {
            $locale = $user->getLanguage();

            $emailBody = $this->translator->trans(
                'password_change.notification',
                [
                    '%username%' => $user->getUsername(),
                    '%date%' => (new \DateTime())->format('d/m/Y H:i:s')
                ],
                'UserEmail',
                $locale
            );

            $email = (new Email())
                ->to($user->getEmail())
                ->subject($this->translator->trans('password_change.subject', [], 'UserEmail', $locale))
                ->text($emailBody)
                ->html($emailBody);

            $this->mailer->send($email);

            $this->logger->info('Password change notification sent successfully', [
                'user_id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail()
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Error sending password change notification', [
                'user_id' => $user->getId(),
                'username' => $user->getUsername(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function enforceSecurityMeasures(PasswordChangedEvent $event): void
    {
        $user = $event->getUser();

        $this->logger->debug('Applying security measures after password change', [
            'user_id' => $user->getId(),
            'username' => $user->getUsername()
        ]);

        // Revoke all refresh tokens for JWT authentication
        $username = $user->getUsername();

        $tokens = $this->entityManager->getRepository(RefreshToken::class)
            ->createQueryBuilder('t')
            ->where('t.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getResult();

        foreach ($tokens as $token) {
            $this->refreshTokenManager->delete($token);
        }

        $this->logger->info('Revoked all JWT refresh tokens after password change', [
            'user_id' => $user->getId(),
            'token_count' => count($tokens)
        ]);
    }
}
