<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\User\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\Client;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BoundedContext\User\Tests\Factory\UserFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AbstractFunctionalTestCase extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    protected Client $apiClient;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure DAMA transaction is started
        //StaticDriver::setKeepStaticConnections(true);

        $this->apiClient = static::createClient();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Clean up any remaining connections
        //StaticDriver::setKeepStaticConnections(false);
    }
}
