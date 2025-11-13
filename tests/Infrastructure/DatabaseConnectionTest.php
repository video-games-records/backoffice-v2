<?php

// tests/Infrastructure/DatabaseConnectionTest.php
namespace App\Tests\Infrastructure;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DatabaseConnectionTest extends KernelTestCase
{
    public function testDatabaseConnection(): void
    {
        self::bootKernel();

        /** @var Connection $connection */
        $connection = self::getContainer()->get('doctrine.dbal.default_connection');

        $this->assertInstanceOf(Connection::class, $connection);

        // Basic connection test
        $result = $connection->executeQuery('SELECT 1 as test')->fetchAssociative();
        $this->assertEquals(['test' => 1], $result);
    }

    public function testEntityManagerIsAvailable(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        $this->assertNotNull($entityManager);
        $this->assertTrue($entityManager->isOpen());
    }
}
