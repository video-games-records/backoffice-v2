<?php

declare(strict_types=1);

namespace App\Tests\Presentation\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

abstract class AbstractWebTestCase extends WebTestCase
{
    use Factories;
}
