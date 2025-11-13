<?php

// tests/Unit/KernelTest.php
namespace App\Tests\Unit;

use App\Kernel;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    public function testKernelInstantiation(): void
    {
        $kernel = new Kernel('test', true);

        $this->assertInstanceOf(Kernel::class, $kernel);
        $this->assertEquals('test', $kernel->getEnvironment());
        $this->assertTrue($kernel->isDebug());
    }

    public function testKernelProjectDir(): void
    {
        $kernel = new Kernel('test', true);

        $projectDir = $kernel->getProjectDir();
        $this->assertIsString($projectDir);
        $this->assertDirectoryExists($projectDir);
        $this->assertTrue(str_ends_with($projectDir, 'symfony-skeleton') || str_contains($projectDir, 'app'));
    }
}
