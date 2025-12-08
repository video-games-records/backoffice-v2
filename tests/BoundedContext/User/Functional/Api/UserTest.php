<?php

namespace App\Tests\BoundedContext\User\Functional\Api;

use App\BoundedContext\User\Domain\Entity\User;

class UserTest extends AbstractFunctionalTestCase
{
    public function testGetCollection(): void
    {
        $response = $this->apiClient->request('GET', '/api/users');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }

    public function testCreateUser(): void
    {
        $unique = uniqid();
        $response = $this->apiClient->request('POST', '/api/users', [
            'json' => [
                'username' => "sancho{$unique}",
                'plainPassword' => 'sancho',
                'email' => "sancho{$unique}@sancho.fr",
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'User',
            'lastLogin' => null,
            'nbConnexion' => 0,
            'language' => 'en',
            'slug' => "sancho{$unique}",
        ]);
        $this->assertMatchesRegularExpression('~^/api/users/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(
            User::class,
            null,
            'jsonld',
            ['groups' => ['user:read']]
        );
    }

    public function testUpdateUser(): void
    {
        $user = $this->createUser();
        $this->authenticateUser($user);
        
        $unique = uniqid();
        $newUsername = "updated_user_{$unique}";
        $newEmail = "updated_user_{$unique}@example.com";
        
        $response = $this->apiClient->request('PUT', '/api/users/' . $user->getId(), [
            'json' => [
                'username' => $newUsername,
                'email' => $newEmail,
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'User',
            'username' => $newUsername,
        ]);
        $this->assertMatchesResourceItemJsonSchema(
            User::class,
            null,
            'jsonld',
            ['groups' => ['user:read']]
        );
    }
}
