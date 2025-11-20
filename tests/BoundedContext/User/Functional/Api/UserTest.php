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
}
