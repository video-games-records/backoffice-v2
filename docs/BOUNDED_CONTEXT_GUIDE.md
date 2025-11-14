# Guide : Ajouter un nouveau BoundedContext

Ce guide décrit les étapes pour ajouter un nouveau contexte métier (BoundedContext) dans l'architecture DDD du projet.

## Vue d'ensemble de l'architecture

Le projet utilise une architecture **Domain-Driven Design (DDD)** avec des **Bounded Contexts** organisés selon les couches suivantes :

```
src/BoundedContext/{ContextName}/
├── Domain/                     # Logique métier pure
│   ├── Entity/                 # Entités du domaine
│   └── Repository/             # Interfaces des repositories
├── Infrastructure/             # Couche technique
│   └── Doctrine/               # Implémentations Doctrine
│       ├── Repository/         # Repositories concrets
│       └── EventListener/      # Entity Listeners Doctrine
├── Application/                # Orchestration et services
│   ├── Service/                # Services applicatifs
│   └── Command/                # Commandes console
├── Presentation/               # Couche présentation
│   ├── Admin/                  # Classes Admin Sonata
│   ├── Api/Controller/         # API Controllers
│   ├── Web/Controller/         # Web Controllers
│   └── Resources/              # Templates et traductions
│       ├── views/              # Templates Twig
│       └── translations/       # Fichiers de traduction
└── Tests/                      # Tests du contexte
    ├── Factory/                # Foundry Factories
    └── Story/                  # Foundry Stories
```

## Étapes d'ajout d'un nouveau BoundedContext

### 1. Créer la structure des dossiers

```bash
mkdir -p src/BoundedContext/{ContextName}/{Domain/{Entity,Repository},Infrastructure/Doctrine/{Repository,EventListener},Application/{Service,Command},Presentation/{Admin,Api/Controller,Web/Controller,Resources/{views,translations}},Tests/{Factory,Story}}
```

### 2. Entité du domaine

Créer l'entité principale dans `src/BoundedContext/{ContextName}/Domain/Entity/`:

```php
<?php

namespace App\BoundedContext\{ContextName}\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\BoundedContext\{ContextName}\Infrastructure\Doctrine\Repository\{EntityName}Repository;

#[ORM\Table(name: 'prefix_{table_name}')]
#[ORM\Entity(repositoryClass: {EntityName}Repository::class)]
#[ApiResource(
    // Configuration API Platform
)]
class {EntityName}
{
    // Propriétés et méthodes
}
```

### 3. Interface Repository

Créer l'interface dans `src/BoundedContext/{ContextName}/Domain/Repository/`:

```php
<?php

namespace App\BoundedContext\{ContextName}\Domain\Repository;

interface {EntityName}RepositoryInterface
{
    // Méthodes métier spécifiques
}
```

### 4. Repository Doctrine

Implémenter le repository dans `src/BoundedContext/{ContextName}/Infrastructure/Doctrine/Repository/`:

```php
<?php

namespace App\BoundedContext\{ContextName}\Infrastructure\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\BoundedContext\{ContextName}\Domain\Entity\{EntityName};
use App\BoundedContext\{ContextName}\Domain\Repository\{EntityName}RepositoryInterface;

class {EntityName}Repository extends ServiceEntityRepository implements {EntityName}RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, {EntityName}::class);
    }

    // Implémentation des méthodes
}
```

### 5. Event Listeners (optionnel)

Si nécessaire, créer des listeners dans `src/BoundedContext/{ContextName}/Infrastructure/Doctrine/EventListener/`:

```php
<?php

namespace App\BoundedContext\{ContextName}\Infrastructure\Doctrine\EventListener;

use App\BoundedContext\{ContextName}\Domain\Entity\{EntityName};

readonly class {EntityName}Listener
{
    public function __construct(
        // Dependencies
    ) {
    }

    public function prePersist({EntityName} $entity): void
    {
        // Logique avant persistance
    }
}
```

### 6. Services applicatifs (optionnel)

Créer les services dans `src/BoundedContext/{ContextName}/Application/Service/`:

```php
<?php

namespace App\BoundedContext\{ContextName}\Application\Service;

class {ServiceName}
{
    // Services applicatifs
}
```

### 7. Controllers API

Créer les controllers dans `src/BoundedContext/{ContextName}/Presentation/Api/Controller/`:

```php
<?php

namespace App\BoundedContext\{ContextName}\Presentation\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class {ActionName} extends AbstractController
{
    public function __invoke(): Response
    {
        // Logique du controller
    }
}
```

### 8. Admin Sonata (optionnel)

Créer les classes admin dans `src/BoundedContext/{ContextName}/Presentation/Admin/`:

```php
<?php

namespace App\BoundedContext\{ContextName}\Presentation\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;

class {EntityName}Admin extends AbstractAdmin
{
    // Configuration Sonata Admin
}
```

### 9. Configuration Doctrine

Ajouter le mapping dans `config/packages/doctrine.yaml` :

```yaml
doctrine:
    orm:
        entity_managers:
            default:
                mappings:
                    # ... autres mappings
                    {ContextName}Domain:
                        type: attribute
                        is_bundle: false
                        dir: '%kernel.project_dir%/src/BoundedContext/{ContextName}/Domain'
                        prefix: 'App\BoundedContext\{ContextName}\Domain'
                        alias: {ContextName}Domain
```

### 10. Configuration des services

Créer `config/services/{context_name}.yaml` :

```yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    # Domain Services
    App\BoundedContext\{ContextName}\Domain\:
        resource: '../../src/BoundedContext/{ContextName}/Domain/*'
        exclude: '../../src/BoundedContext/{ContextName}/Domain/{Entity,ValueObject,Event,Exception}'

    # Application Layer
    App\BoundedContext\{ContextName}\Application\:
        resource: '../../src/BoundedContext/{ContextName}/Application/*'

    # Infrastructure Layer
    App\BoundedContext\{ContextName}\Infrastructure\:
        resource: '../../src/BoundedContext/{ContextName}/Infrastructure/*'

    # Presentation Layer
    App\BoundedContext\{ContextName}\Presentation\:
        resource: '../../src/BoundedContext/{ContextName}/Presentation/*'

    # Specific services
    App\BoundedContext\{ContextName}\Infrastructure\Doctrine\Repository\{EntityName}Repository:
        tags: ['doctrine.repository_service']

    App\BoundedContext\{ContextName}\Infrastructure\Doctrine\EventListener\{EntityName}Listener:
        tags: ['doctrine.orm.entity_listener']
```

### 11. Importer la configuration des services

Ajouter dans `config/services.yaml` :

```yaml
imports:
    # ... autres imports
    - { resource: services/{context_name}.yaml }
```

### 12. Configuration Sonata Admin (optionnel)

Créer `config/admin/{context_name}.yml` :

```yaml
services:
    App\BoundedContext\{ContextName}\Presentation\Admin\{EntityName}Admin:
        arguments:
            - ~
            - App\BoundedContext\{ContextName}\Domain\Entity\{EntityName}
            - ~
        calls:
            - [setTranslationDomain, ['{translation_domain}']]
        tags:
            - { name: sonata.admin, model_class: 'App\BoundedContext\{ContextName}\Domain\Entity\{EntityName}', group: '{Group}', label: '{Label}' }
```

Puis importer dans `config/services.yaml` :

```yaml
imports:
    # ... autres imports
    - { resource: admin/{context_name}.yml }
```

### 13. Configuration Twig (optionnel)

Ajouter le namespace dans `config/packages/twig.yaml` :

```yaml
twig:
    paths:
        # ... autres paths
        '%kernel.project_dir%/src/BoundedContext/{ContextName}/Presentation/Resources/views': '{ContextName}'
```

### 14. Fichiers de traduction (optionnel)

Créer les traductions dans `src/BoundedContext/{ContextName}/Presentation/Resources/translations/` :

- `messages.fr.yml`
- `SonataAdminBundle.fr.yml` (si admin)

### 15. Routes personnalisées (optionnel)

Créer `config/routes/{context_name}.yaml` si nécessaire :

```yaml
{route_name}:
    path: /api/{context}/custom-endpoint
    controller: App\BoundedContext\{ContextName}\Presentation\Api\Controller\{ControllerName}
    methods: [GET]
```

### 16. Tests

#### Factory Foundry

Créer `src/BoundedContext/{ContextName}/Tests/Factory/{EntityName}Factory.php` :

```php
<?php

namespace App\BoundedContext\{ContextName}\Tests\Factory;

use App\BoundedContext\{ContextName}\Domain\Entity\{EntityName};
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class {EntityName}Factory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return {EntityName}::class;
    }

    protected function defaults(): array|callable
    {
        return [
            // Valeurs par défaut
        ];
    }

    // Méthodes utilitaires
}
```

#### Story Foundry

Créer `src/BoundedContext/{ContextName}/Tests/Story/{ContextName}Story.php` :

```php
<?php

namespace App\BoundedContext\{ContextName}\Tests\Story;

use Zenstruck\Foundry\Story;

final class {ContextName}Story extends Story
{
    public function build(): void
    {
        // Création des données de test
    }

    // Méthodes statiques pour récupérer des entités spécifiques
}
```

#### Fixtures

Créer `src/DataFixtures/{ContextName}Fixtures.php` :

```php
<?php

namespace App\DataFixtures;

use App\BoundedContext\{ContextName}\Tests\Story\{ContextName}Story;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class {ContextName}Fixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        {ContextName}Story::load();
        // Références si nécessaire
    }

    public function getDependencies(): array
    {
        return [
            // Dépendances vers autres fixtures
        ];
    }
}
```

#### Tests API

Créer la structure de test dans `tests/BoundedContext/{ContextName}/Functional/Api/` :

```php
<?php

namespace App\Tests\BoundedContext\{ContextName}\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class {EntityName}Test extends AbstractFunctionalTestCase
{
    // Tests des endpoints API
}
```

### 17. Mise à jour de la base de données

```bash
# Créer/mettre à jour le schéma
php bin/console doctrine:schema:update --force

# Charger les fixtures
php bin/console doctrine:fixtures:load --no-interaction
```

### 18. Configuration du Makefile

Ajouter les cibles de test dans le `Makefile` :

```makefile
# Ajouter dans la section .PHONY
.PHONY: ... test-{context_name} test-{context_name}-api ...

# Ajouter les cibles de test
test-{context_name}: ## Run all {ContextName} BoundedContext tests
	@echo "$(GREEN)Running {ContextName} BoundedContext tests...$(RESET)"
	$(PHPUNIT_BIN) tests/BoundedContext/{ContextName}/ --testdox

test-{context_name}-api: ## Run {ContextName} API tests only
	@echo "$(GREEN)Running {ContextName} API tests...$(RESET)"
	@echo "$(YELLOW)Clearing rate limiter cache...$(RESET)"
	$(CONSOLE_BIN) cache:pool:clear cache.rate_limiter --env=test
	$(PHPUNIT_BIN) tests/BoundedContext/{ContextName}/Functional/Api/ --testdox

# Ajouter les raccourcis dans la section "Quick Commands"
t{context_initial}: test-{context_name} ## Shortcut for test-{context_name}
t{context_initial}-api: test-{context_name}-api ## Shortcut for test-{context_name}-api
```

### 19. Configuration API Platform

Ajouter le chemin des entités dans `config/packages/api_platform.yaml` :

```yaml
api_platform:
    resource_class_directories:
        - '%kernel.project_dir%/src/BoundedContext/User/Domain'
        - '%kernel.project_dir%/src/BoundedContext/Article/Domain'
        - '%kernel.project_dir%/src/BoundedContext/{ContextName}/Domain'
        - '%kernel.project_dir%/src/SharedKernel/Domain'
```

### 20. Validation

```bash
# Vérifier les entités
php bin/console doctrine:mapping:info

# Vider le cache après configuration API Platform
php bin/console cache:clear --env=test

# Vérifier les routes API
php bin/console debug:router | grep {context}

# Lancer les tests avec le Makefile
make test-{context_name}
make test-{context_name}-api

# Ou avec les raccourcis
make t{context_initial}
make t{context_initial}-api
```

## Checklist de validation

- [ ] Structure des dossiers créée
- [ ] Entité du domaine créée avec attributs Doctrine et API Platform
- [ ] Interface Repository définie
- [ ] Repository Doctrine implémenté
- [ ] Event Listeners configurés (si nécessaire)
- [ ] Services applicatifs créés (si nécessaire)
- [ ] Controllers API créés
- [ ] Configuration Doctrine ajoutée
- [ ] Configuration des services ajoutée et importée
- [ ] Configuration Sonata Admin ajoutée (si nécessaire)
- [ ] Namespace Twig configuré (si nécessaire)
- [ ] Routes personnalisées créées (si nécessaire)
- [ ] Factory Foundry créée
- [ ] Story Foundry créée
- [ ] Fixtures créées
- [ ] Tests API créés
- [ ] Configuration Makefile ajoutée
- [ ] Configuration API Platform ajoutée
- [ ] Schéma de base de données mis à jour
- [ ] Cache vidé après configuration
- [ ] Routes API Platform générées
- [ ] Tests passent avec succès
- [ ] Routes API fonctionnelles

## Bonnes pratiques

1. **Nommage** : Utiliser PascalCase pour les noms de contextes (ex: `Message`, `User`, `Article`)
2. **Isolation** : Chaque contexte doit être autonome et ne pas dépendre directement d'autres contextes
3. **Tests** : Écrire des tests complets pour chaque endpoint API
4. **Documentation** : Documenter les endpoints API avec les annotations appropriées
5. **Traductions** : Ajouter les traductions pour tous les textes visibles
6. **Sécurité** : Configurer les permissions API Platform appropriées

## Exemple complet

Voir le contexte `Message` comme exemple de référence complet d'implémentation.