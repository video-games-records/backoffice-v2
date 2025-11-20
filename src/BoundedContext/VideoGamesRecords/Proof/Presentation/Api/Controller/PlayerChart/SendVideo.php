<?php

declare(strict_types=1);

namespace App\BoundedContext\VideoGamesRecords\Proof\Presentation\Api\Controller\PlayerChart;

use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChart;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\PlayerChartStatus;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\Security\UserProvider;
use App\BoundedContext\VideoGamesRecords\Proof\Domain\Entity\Proof;
use App\BoundedContext\VideoGamesRecords\Proof\Domain\Entity\Video;
use App\BoundedContext\VideoGamesRecords\Proof\Domain\ValueObject\VideoType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class SendVideo extends AbstractController
{
    private UserProvider $userProvider;
    private EntityManagerInterface $em;

    public function __construct(
        UserProvider $userProvider,
        EntityManagerInterface $em
    ) {
        $this->userProvider = $userProvider;
        $this->em = $em;
    }

    /**
     * @param PlayerChart $playerChart
     * @param Request     $request
     * @return Proof
     * @throws AccessDeniedException|ORMException
     */
    public function __invoke(PlayerChart $playerChart, Request $request): Proof
    {
        $player = $this->userProvider->getPlayer();

        if ($playerChart->getPlayer() !== $player) {
            throw new AccessDeniedException('ACCESS DENIED');
        }
        if (!in_array($playerChart->getStatus()->getId(), PlayerChartStatus::getStatusForProving())) {
            throw new AccessDeniedException('ACCESS DENIED');
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['url'])) {
            throw new BadRequestException('Video URL is required');
        }

        $video = new Video();
        $video->setUrl($data['url']);
        $video->setType(new VideoType($data['type'] ?? VideoType::YOUTUBE));
        $video->setPlayer($player);
        $video->setGame($playerChart->getChart()->getGroup()->getGame());

        $this->em->persist($video);

        $proof = new Proof();
        $proof->setVideo($video);
        $proof->setPlayer($player);
        $proof->setChart($playerChart->getChart());
        $this->em->persist($proof);

        $playerChart->setProof($proof);
        if ($playerChart->getStatus()->getId() === PlayerChartStatus::ID_STATUS_NORMAL) {
            $playerChart->setStatus(
                $this->em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NORMAL_SEND_PROOF)
            );
        } else {
            $playerChart->setStatus(
                $this->em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_DEMAND_SEND_PROOF)
            );
        }

        $this->em->flush();

        return $proof;
    }
}
