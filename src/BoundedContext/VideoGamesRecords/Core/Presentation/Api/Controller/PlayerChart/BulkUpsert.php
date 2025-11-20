<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Core\Presentation\Api\Controller\PlayerChart;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\Message\Player\UpdatePlayerChartRank;

class BulkUpsert extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private MessageBusInterface $messageBus;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        MessageBusInterface $messageBus,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->messageBus = $messageBus;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_PLAYER');

        $content = json_decode($request->getContent(), true);

        if (!isset($content['playerCharts']) || !is_array($content['playerCharts'])) {
            return new JsonResponse(['error' => 'playerCharts array is required'], Response::HTTP_BAD_REQUEST);
        }

        $playerCharts = [];
        $errors = [];
        $chartIds = [];
        $createdCount = 0;
        $updatedCount = 0;

        $this->entityManager->getConnection()->beginTransaction();

        try {
            foreach ($content['playerCharts'] as $index => $playerChartData) {
                $result = $this->processPlayerChartData($playerChartData, $index, $errors);

                if ($result !== null) {
                    $this->entityManager->persist($result['playerChart']);
                    $playerCharts[] = $result['playerChart'];
                    $chartIds[] = $result['playerChart']->getChart()->getId();

                    if ($result['isUpdate']) {
                        $updatedCount++;
                    } else {
                        $createdCount++;
                    }
                }
            }

            if (!empty($errors)) {
                $this->entityManager->getConnection()->rollBack();
                return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
            }

            $this->entityManager->flush();

            if (!empty($playerCharts)) {
                $lastPlayerChart = end($playerCharts);
                $game = $lastPlayerChart->getChart()->getGroup()->getGame();
                $game->setLastScore($lastPlayerChart);
                $this->entityManager->flush();
            }

            $this->entityManager->getConnection()->commit();

            foreach (array_unique($chartIds) as $chartId) {
                $this->messageBus->dispatch(new UpdatePlayerChartRank($chartId));
            }

            return new JsonResponse([
                'success' => true,
                'created' => $createdCount,
                'updated' => $updatedCount,
                'total' => count($playerCharts)
            ]);
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $errors
     * @return array<string, mixed>|null
     */
    private function processPlayerChartData(array $data, int $index, array &$errors): ?array
    {
        // Implementation simplifiée pour la création/mise à jour des PlayerChart
        // Cette méthode devrait être complétée selon la logique métier
        return null;
    }
}
