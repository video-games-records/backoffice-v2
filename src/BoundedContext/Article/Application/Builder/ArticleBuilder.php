<?php

declare(strict_types=1);

namespace App\BoundedContext\Article\Application\Builder;

use App\BoundedContext\Article\Domain\Entity\Article;
use App\BoundedContext\Article\Domain\ValueObject\ArticleStatus;
use App\BoundedContext\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ArticleBuilder
{
    private ?User $author = null;

    /**
     * @var string[]
     */
    private array $titles = [];

    /**
     * @var string[]
     */
    private array $contents = [];

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function setAuthor(User $author): ArticleBuilder
    {
        $this->author = $author;
        return $this;
    }

    public function setTitle(string $title, string $lang): ArticleBuilder
    {
        $this->titles[$lang] = $title;
        return $this;
    }

    public function setContent(string $content, string $lang): ArticleBuilder
    {
        $this->contents[$lang] = $content;
        return $this;
    }

    public function send(): void
    {
        $article = new Article();
        $article->setAuthor($this->author);
        $article->setStatus(ArticleStatus::PUBLISHED);
        $article->setPublishedAt(new \DateTime());

        foreach ($this->titles as $lang => $value) {
            $article->setTitle($value, $lang);
        }
        foreach ($this->contents as $lang => $value) {
            $article->setContent($value, $lang);
        }

        $this->em->persist($article);
        $this->em->flush();
    }
}
