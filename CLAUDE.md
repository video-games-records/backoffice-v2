# Architecture DDD - Domain-Driven Design

Ce projet utilise une architecture **Domain-Driven Design (DDD)** avec des **Bounded Contexts** pour organiser le code selon les domaines métier.

## Structure Générale

```
src/
├── SharedKernel/                           # Code partagé entre tous les contextes
│   ├── Domain/                            # Concepts partagés du domaine
│   │   └── Security/                      # Ex: SecurityEventTypeEnum
│   ├── Infrastructure/                    # Couches techniques partagées
│   └── Presentation/
│       └── Web/
│           ├── Controller/                # Controllers transversaux (ex: LogReaderController)
│           └── Resources/views/           # Templates transversaux (layout, base.html.twig)
│
└── BoundedContext/                        # Contextes métier délimités
    └── User/                              # Contexte de gestion des utilisateurs
        ├── Domain/
        │   ├── Entity/                    # Entités métier (User, SecurityEvent, Group)
        │   └── Repository/                # Interfaces des repositories
        ├── Infrastructure/
        │   ├── Doctrine/                  # Implémentations Doctrine
        │   │   └── Repository/            # Repositories concrets
        │   └── Admin/
        │       └── Extension/             # Extensions Sonata Admin
        ├── Application/
        │   └── Service/                   # Services applicatifs
        └── Presentation/
            ├── Admin/                     # Classes Admin Sonata
            ├── Api/
            │   └── Controller/            # API Controllers
            ├── Web/
            │   └── Controller/            # Web Controllers
            └── Resources/
                └── views/                 # Templates spécifiques au contexte User
```

## Principes DDD Appliqués

### 1. Bounded Contexts (Contextes Délimités)
- **User Context** : Gestion des utilisateurs, groupes, événements de sécurité
- **Autres contextes** peuvent être ajoutés (Product, Order, etc.)

### 2. SharedKernel (Noyau Partagé)
- Contient les concepts partagés entre contextes
- Fonctionnalités transversales (logs, layouts admin)
- Éviter les duplications entre contextes

### 3. Couches DDD
- **Domain** : Logique métier pure, entités, value objects
- **Application** : Orchestration, services applicatifs  
- **Infrastructure** : Persistence, APIs externes
- **Presentation** : Controllers, vues, APIs

## Exemples de Composants

### Contexte User
```php
// Domain Layer
src/BoundedContext/User/Domain/Entity/User.php
src/BoundedContext/User/Domain/Entity/SecurityEvent.php

// Infrastructure Layer  
src/BoundedContext/User/Infrastructure/Doctrine/Repository/UserRepository.php
src/BoundedContext/User/Infrastructure/Admin/Extension/SecurityEventStatisticsExtension.php

// Application Layer
src/BoundedContext/User/Application/Service/UserRegistrationService.php

// Presentation Layer
src/BoundedContext/User/Presentation/Admin/UserAdmin.php
src/BoundedContext/User/Presentation/Web/Controller/Admin/SecurityEventStatisticsController.php
src/BoundedContext/User/Resources/views/admin/security_statistics.html.twig
```

### SharedKernel
```php
// Domaine partagé
src/SharedKernel/Domain/Security/SecurityEventTypeEnum.php

// Infrastructure partagée
src/SharedKernel/Presentation/Web/Controller/LogReaderController.php
src/SharedKernel/Resources/views/base.html.twig
src/SharedKernel/Resources/views/admin/layout.html.twig
```

## Configuration Twig

Les templates sont organisés par namespace :

```yaml
# config/packages/twig.yaml
twig:
    paths:
        '%kernel.project_dir%/src/SharedKernel/Resources/views': 'SharedKernel'
        '%kernel.project_dir%/src/BoundedContext/User/Resources/views': 'User'
```

Utilisation :
```twig
{# Template SharedKernel #}
{% extends '@SharedKernel/admin/layout.html.twig' %}

{# Template User Context #}
{% include '@User/admin/security_statistics.html.twig' %}
```

## Bonnes Pratiques

### 1. Isolation des Contextes
- Chaque contexte est autonome
- Pas de dépendances directes entre contextes
- Communication via events ou services partagés

### 2. Couches Respectées  
- Domain ne dépend de rien
- Application dépend du Domain
- Infrastructure dépend de Application et Domain
- Presentation dépend de Application

### 3. Naming Conventions
- Contextes : PascalCase (User, Product, Order)
- Couches : PascalCase (Domain, Infrastructure, etc.)
- Entités : Suffixe explicite (UserAdmin, SecurityEvent)

### 4. Templates
- Templates métier dans leur contexte
- Templates transversaux dans SharedKernel
- Utiliser les namespaces Twig appropriés

## Ajout d'un Nouveau Contexte

Pour ajouter un nouveau contexte (ex: Product) :

```
src/BoundedContext/Product/
├── Domain/
│   ├── Entity/
│   │   └── Product.php
│   └── Repository/
│       └── ProductRepositoryInterface.php
├── Infrastructure/
│   └── Doctrine/
│       └── Repository/
│           └── ProductRepository.php
├── Application/
│   └── Service/
│       └── ProductCatalogService.php
└── Presentation/
    ├── Admin/
    │   └── ProductAdmin.php
    └── Resources/
        └── views/
            └── admin/
                └── product_list.html.twig
```

Puis ajouter le namespace Twig :
```yaml
# config/packages/twig.yaml
'%kernel.project_dir%/src/BoundedContext/Product/Resources/views': 'Product'
```