<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\User\Functional\Api;

use App\BoundedContext\User\Tests\Story\AdminUserStory;
use Datetime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class SecurityTest extends AbstractFunctionalTestCase
{
    private string $token = 'gPlbM4ZJ1ZooU1G8DiTyIfEEY3grjwdgxYA56Scw3mj2tDVIajcFFVKClECZXvJ4';
    private string $password = 'password';

    public function testSendPasswordResetLink(): void
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.async');
        // Clear any existing messages from other tests
        $transport->get(); // This empties the transport

        // Create an admin user for the test
        $unique = uniqid();
        $adminEmail = "admin{$unique}@local.fr";
        $adminUser = $this->createUser(['email' => $adminEmail, 'username' => "admin{$unique}"]);

        $response = $this->apiClient->request('POST', '/api/users/reset-password/send-link', ['json' => [
            'email' => $adminEmail,
            'callBackUrl' => ''
        ]]);
        $this->assertResponseIsSuccessful();

        // Get admin user from database
        $user = $adminUser->_real();
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->refresh($user);

        $this->assertNotNull($user->getConfirmationToken());
        $this->assertNotNull($user->getPasswordRequestedAt());

        // Check that at least one message was sent (password reset email)
        $messages = $transport->get();
        $this->assertGreaterThanOrEqual(1, count($messages));
    }



    public function testConfirmPassword(): void
    {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get('doctrine.orm.entity_manager');

        // Create an admin user for the test
        $unique = uniqid();
        $adminUser = $this->createUser(['email' => "admin{$unique}@local.fr", 'username' => "admin{$unique}"]);

        // Get admin user from database
        $user = $adminUser->_real();
        $user->setConfirmationToken($this->token);
        $user->setPasswordRequestedAt(new Datetime());
        $oldHashPassword = $user->getPassword();
        $em->flush();

        $response = $this->apiClient->request('POST', '/api/users/reset-password/confirm', ['json' => [
            'token' => $this->token,
            'plainPassword' => $this->password,
        ]]);
        $this->assertResponseIsSuccessful();

        // Refresh user from database
        $em->refresh($user);
        $newHashPassword = $user->getPassword();
        $this->assertNotEquals($oldHashPassword, $newHashPassword);
    }
}
