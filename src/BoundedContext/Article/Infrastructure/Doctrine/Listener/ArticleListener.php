<?php

declare(strict_types=1);

namespace App\BoundedContext\Article\Infrastructure\Doctrine\Listener;

use App\BoundedContext\Article\Domain\Entity\Article;
use App\BoundedContext\User\Domain\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class ArticleListener
{
    public function __construct(
        private Security $security,
        private SluggerInterface $slugger,
        private RequestStack $requestStack
    ) {
    }

    public function prePersist(Article $article): void
    {
        if (null === $article->getAuthor()) {
            $user = $this->security->getUser();
            if ($user instanceof User) {
                $article->setAuthor($user);
            }
        }
        if ($article->getArticleStatus()->isPublished()) {
            $article->setPublishedAt(new \DateTime());
        }

        $this->updateSlug($article);
    }

    public function preUpdate(Article $article, PreUpdateEventArgs $event): void
    {
        if ($article->getArticleStatus()->isPublished() && $article->getPublishedAt() === null) {
            $article->setPublishedAt(new \DateTime());
        }

        $this->updateSlug($article);
    }

    public function postLoad(Article $article, LifecycleEventArgs $event): void
    {
        // Check if we're in test environment and have a forced locale
        if (class_exists('App\Tests\SharedKernel\Domain\Service\TestLocaleResolver')) {
            $forcedLocale = \App\Tests\SharedKernel\Domain\Service\TestLocaleResolver::getForcedLocale();
            if ($forcedLocale) {
                $article->setCurrentLocale($forcedLocale);
                return;
            }
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $article->setCurrentLocale($request->getLocale());
        }
    }

    private function updateSlug(Article $article): void
    {
        $article->setSlug($this->slugger->slug($article->getDefaultTitle())->lower()->toString());
    }
}
