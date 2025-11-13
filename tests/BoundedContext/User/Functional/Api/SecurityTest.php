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
        // Load the admin user story to have consistent test data
        AdminUserStory::load();

        /** @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.async');
        // Clear any existing messages from other tests
        $transport->get(); // This empties the transport

        $response = $this->apiClient->request('POST', '/api/users/reset-password/send-link', ['json' => [
            'email' => 'admin@local.fr',
            'callBackUrl' => ''
        ]]);
        $this->assertResponseIsSuccessful();

        // Get admin user from story
        $user = AdminUserStory::adminUser()->_real();
        $this->assertNotNull($user->getConfirmationToken());
        $this->assertNotNull($user->getPasswordRequestedAt());

        // Check that at least one message was sent (password reset email)
        $messages = $transport->get();
        $this->assertGreaterThanOrEqual(1, count($messages));
    }



    public function testConfirmPassword(): void
    {
        // Load the admin user story to have consistent test data
        AdminUserStory::load();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get('doctrine.orm.entity_manager');

        // Get admin user from story
        $user = AdminUserStory::adminUser()->_real();
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
