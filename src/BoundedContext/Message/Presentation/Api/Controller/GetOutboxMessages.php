<?php

declare(strict_types=1);

namespace App\BoundedContext\Message\Presentation\Api\Controller;

use ApiPlatform\Doctrine\Orm\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use App\BoundedContext\Message\Infrastructure\Doctrine\Repository\MessageRepository;
use App\BoundedContext\User\Domain\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GetOutboxMessages extends AbstractController
{
    private MessageRepository $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function __invoke(Request $request): Paginator
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException('User must be logged in.');
        }

        // Récupérer les paramètres de pagination
        $page = $request->query->getInt('page', 1);
        $itemsPerPage = $request->query->getInt('itemsPerPage', 10);

        // Récupérer les filtres
        $filters = [];
        if ($request->query->has('search')) {
            $filters['search'] = $request->query->get('search');
        }
        if ($request->query->has('type')) {
            $filters['type'] = $request->query->get('type');
        }
        if ($request->query->has('recipient')) {
            $filters['recipient'] = $request->query->get('recipient');
        }
        if ($request->query->has('isOpened')) {
            $filters['isOpened'] = $request->query->getBoolean('isOpened');
        }

        $queryBuilder = $this->messageRepository->getOutboxMessages($user, $filters);

        // Appliquer la pagination manuellement
        $firstResult = ($page - 1) * $itemsPerPage;
        $queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($itemsPerPage);

        return new Paginator(new DoctrinePaginator($queryBuilder));
    }
}
