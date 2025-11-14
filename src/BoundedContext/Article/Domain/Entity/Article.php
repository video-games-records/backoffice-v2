<?php

declare(strict_types=1);

namespace App\BoundedContext\Article\Domain\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use App\BoundedContext\Article\Domain\ValueObject\ArticleStatus;
use App\BoundedContext\Article\Infrastructure\Filter\TranslationSearchFilter;
use App\BoundedContext\User\Domain\Entity\User;
use App\SharedKernel\Domain\Entity\TimestampableTrait;
use App\SharedKernel\Domain\Entity\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

#[ORM\Table(name:'pna_article')]
#[ORM\Entity(repositoryClass: 'App\BoundedContext\Article\Infrastructure\Doctrine\Repository\ArticleRepository')]
#[ORM\HasLifecycleCallbacks]
#[ORM\EntityListeners(['App\BoundedContext\Article\Infrastructure\Doctrine\Listener\ArticleListener'])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ],
    normalizationContext: ['groups' => ['article:read', 'article:author']],
    order: ['publishedAt' => 'DESC']
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'status' => 'exact',
    ]
)]
#[ApiFilter(TranslationSearchFilter::class)]
class Article
{
    use TimestampableTrait;
    use TranslatableTrait;


    #[Groups(['article:read'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string', enumType: ArticleStatus::class)]
    private ArticleStatus $status = ArticleStatus::UNDER_CONSTRUCTION;

    #[Groups(['article:read'])]
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbComment = 0;

    #[Groups(['article:read'])]
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $views = 0;

    #[Groups(['article:author'])]
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name:'author_id', referencedColumnName:'id', nullable:false)]
    private User $author;

    #[Groups(['article:read'])]
    #[ORM\Column(nullable: true)]
    private ?DateTime $publishedAt = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Comment::class)]
    private Collection $comments;

    /** @var Collection<string, ArticleTranslation> */
    #[ORM\OneToMany(
        mappedBy: 'translatable',
        targetEntity: ArticleTranslation::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
        indexBy: 'locale'
    )]
    private Collection $translations;

    #[Groups(['article:read'])]
    #[ORM\Column(length: 255, unique: false)]
    private string $slug;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf('%s [%s]', $this->getDefaultTitle(), $this->id);
    }

    public function getDefaultTitle(): string
    {
        return $this->getTitle('en') ?: 'Untitled';
    }

    public function getDefaultContent(): string
    {
        return $this->getContent('en') ?: '';
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setStatus(ArticleStatus $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): ArticleStatus
    {
        return $this->status;
    }

    public function getArticleStatus(): ArticleStatus
    {
        return $this->status;
    }

    public function setNbComment(int $nbComment): void
    {
        $this->nbComment = $nbComment;
    }

    public function getNbComment(): int
    {
        return $this->nbComment;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function setViews(int $views): void
    {
        $this->views = $views;
    }

    public function incrementViews(): void
    {
        $this->views++;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTime $publishedAt = null): void
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @param Collection<int, Comment> $comments
     */
    public function setComments(Collection $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return Collection<string, ArticleTranslation>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    /**
     * @param Collection<string, ArticleTranslation> $translations
     */
    public function setTranslations(Collection $translations): void
    {
        $this->translations = $translations;
    }

    protected function hasTranslationContent(object $translation): bool
    {
        return !empty($translation->getTitle()) || !empty($translation->getContent());
    }

    protected function setTranslatableOnTranslation(object $translation): void
    {
        $translation->setTranslatable($this);
    }

    protected function getTranslationLocale(object $translation): string
    {
        return $translation->getLocale();
    }


    public function setTitle(string $title, ?string $locale = null): void
    {
        $locale = $locale ?: $this->getCurrentLocale() ?: 'en';

        if (!$this->translations->containsKey($locale)) {
            $translation = new ArticleTranslation();
            $translation->setTranslatable($this);
            $translation->setLocale($locale);
            $this->translations->set($locale, $translation);
        }

        $this->translations->get($locale)->setTitle($title);
    }

    #[Groups(['article:read'])]
    public function getTitle(?string $locale = null): ?string
    {
        $translation = $this->translate($locale);
        return $translation?->getTitle();
    }

    public function setContent(string $content, ?string $locale = null): void
    {
        $locale = $locale ?: $this->getCurrentLocale() ?: 'en';

        if (!$this->translations->containsKey($locale)) {
            $translation = new ArticleTranslation();
            $translation->setTranslatable($this);
            $translation->setLocale($locale);
            $this->translations->set($locale, $translation);
        }

        $this->translations->get($locale)->setContent($content);
    }

    #[Groups(['article:read'])]
    public function getContent(?string $locale = null): ?string
    {
        $translation = $this->translate($locale);
        return $translation?->getContent();
    }

    public function mergeNewTranslations(): void
    {
        // Not needed anymore
    }
}
