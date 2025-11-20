<?php

declare(strict_types=1);

namespace App\SharedKernel\Presentation\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use App\SharedKernel\Domain\Contracts\SecurityInterface;
use App\SharedKernel\Domain\Traits\Accessor\SetSecurity;
use App\SharedKernel\Domain\Traits\Accessor\SetRequestStack;
use App\SharedKernel\Domain\Traits\Accessor\SetEventDispatcher;

abstract class BaseAdmin extends AbstractAdmin implements SecurityInterface
{
    use SetSecurity;
    use SetRequestStack;
    use SetEventDispatcher;
}
