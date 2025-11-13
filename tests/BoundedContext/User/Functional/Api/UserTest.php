<?php

namespace App\Tests\BoundedContext\User\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BoundedContext\User\Domain\Entity\User;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserTest extends ApiTestCase
{
    use ResetDatabase;

    public function testGetCollection(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', '/api/users');

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');


        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }

    public function testCreateUser(): void
    {
        $response = static::createClient()->request('POST', '/api/users', [
            'json' => [
                'username' => 'sancho',
                'plainPassword' => 'sancho',
                'email' => 'sancho@sancho.fr',
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'User',
            'id' => 1,
            'lastLogin' => null,
            'nbConnexion' => 0,
            'language' => 'en',
            'slug' => 'sancho',
        ]);
        $this->assertEquals('/api/users/me', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(
            User::class,
            null,
            'jsonld',
            ['groups' => ['user:read']]
        );
    }
}
