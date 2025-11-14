<?php

declare(strict_types=1);

namespace App\BoundedContext\Article\Infrastructure\Doctrine\Listener;

use App\BoundedContext\Article\Domain\Entity\ArticleTranslation;
use Datetime;
use Doctrine\ORM\Event\PostUpdateEventArgs;

class ArticleTranslationListener
{
    public function postUpdate(ArticleTranslation $translation, PostUpdateEventArgs $event): void
    {
        $article = $translation->getTranslatable();

        $em = $event->getObjectManager();

        $article->setUpdatedAt(new DateTime());

        $em->persist($article);
    }
}
