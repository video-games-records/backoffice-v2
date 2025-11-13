<?php

declare(strict_types=1);

namespace App\BoundedContext\User\Presentation\Api\Controller;

use App\BoundedContext\User\Infrastructure\Persistence\Doctrine\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class Autocomplete extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request): mixed
    {
        $q = $request->query->get('query', null);
        return $this->userRepository->autocomplete($q);
    }
}
