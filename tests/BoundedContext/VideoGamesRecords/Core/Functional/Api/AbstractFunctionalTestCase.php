<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\VideoGamesRecords\Core\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\Client;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;

class AbstractFunctionalTestCase extends ApiTestCase
{
    protected Client $apiClient;

    protected function setUp(): void
    {
        parent::setUp();

        // Enable transaction mode for faster tests
        StaticDriver::setKeepStaticConnections(true);

        $this->apiClient = static::createClient();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Clean up connections
        StaticDriver::setKeepStaticConnections(false);
    }
}
