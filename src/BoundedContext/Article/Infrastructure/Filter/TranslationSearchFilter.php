<?php

declare(strict_types=1);

namespace App\BoundedContext\Article\Infrastructure\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\BoundedContext\Article\Application\Service\LocaleResolver;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

final class TranslationSearchFilter extends AbstractFilter
{
    private RequestStack $requestStack;
    private LocaleResolver $localeResolver;

    public function __construct(
        RequestStack $requestStack,
        LocaleResolver $localeResolver,
    ) {
        $this->requestStack = $requestStack;
        $this->localeResolver = $localeResolver;
        parent::__construct();
    }

    protected function filterProperty(
        string $property,
        mixed $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if ($property !== 'search') {
            return;
        }

        if (empty($value)) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        $locale = $this->localeResolver->getPreferredLocale($request);

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $alias = $queryNameGenerator->generateJoinAlias('translation');
        $parameterName = $queryNameGenerator->generateParameterName('search');

        $queryBuilder
            ->leftJoin($rootAlias . '.translations', $alias)
            ->andWhere(sprintf(
                '(%s.locale = :locale OR %s.locale = :defaultLocale) AND (%s.title LIKE :%s OR %s.content LIKE :%s)',
                $alias,
                $alias,
                $alias,
                $parameterName,
                $alias,
                $parameterName
            ))
            ->setParameter('locale', $locale)
            ->setParameter('defaultLocale', $this->localeResolver->getDefaultLocale())
            ->setParameter($parameterName, '%' . $value . '%');
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'search' => [
                'property' => 'search',
                'type' => 'string',
                'required' => false,
                'description' => 'Search in article title and content (current locale)',
                'openapi' => new Parameter(
                    name: 'search',
                    in: 'query',
                    required: false,
                    description: 'Search in article title and content (current locale)',
                    schema: [
                        'type' => 'string',
                        'example' => 'mot clé recherché'
                    ]
                ),
            ],
        ];
    }
}
