<?php

declare(strict_types=1);

namespace App\BoundedContext\Forum\Presentation\Api\Controller\Topic;

use App\BoundedContext\Forum\Domain\Entity\Topic;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BoundedContext\Forum\Application\Service\TopicReadService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class MarkAsRead extends AbstractController
{
    private TopicReadService $topicReadService;
    private TranslatorInterface $translator;

    public function __construct(TopicReadService $topicReadService, TranslatorInterface $translator)
    {
        $this->topicReadService = $topicReadService;
        $this->translator = $translator;
    }

    /**
     * Marque un topic comme lu pour l'utilisateur connecté
     */
    public function __invoke(Topic $topic): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([
                'error' => $this->translator->trans('error.authentication_required')
            ], 401);
        }

        try {
            $result = $this->topicReadService->markTopicAsRead($user, $topic);

            if ($result['wasAlreadyRead']) {
                return new JsonResponse([
                    'success' => true,
                    'message' => $this->translator->trans('topic.already_read'),
                    'forumAlsoMarkedAsRead' => false
                ]);
            }

            return new JsonResponse([
                'success' => true,
                'message' => $this->translator->trans('topic.marked_as_read'),
                'forumAlsoMarkedAsRead' => $result['forumMarkedAsRead']
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $this->translator->trans('error.mark_as_read_failed')
            ], 500);
        }
    }
}
