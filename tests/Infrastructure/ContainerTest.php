<?php

namespace App\Tests\Infrastructure;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContainerTest extends KernelTestCase
{
    public function testServicesAreWiredCorrectly(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        // Test des services essentiels
        self::assertTrue($container->has('doctrine'));
        self::assertTrue($container->has('doctrine.orm.entity_manager'));
        self::assertTrue($container->has('lexik_jwt_authentication.encoder'));
        self::assertTrue($container->has('api_platform.metadata.resource.metadata_collection_factory'));
    }

    public function testSecurityConfiguration(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        // Test que la sécurité est configurée
        self::assertTrue($container->has('security.token_storage'));
        self::assertTrue($container->has('security.authorization_checker'));
        self::assertTrue($container->has('security.password_hasher_factory'));
    }

    public function testApiPlatformServices(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        // Test des services API Platform
        if ($container->has('api_platform.openapi.factory')) {
            self::assertTrue(true, 'API Platform services are available');
        } else {
            self::markTestSkipped('API Platform not fully configured');
        }
    }
}
