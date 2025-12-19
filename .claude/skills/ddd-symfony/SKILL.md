# DDD Pragmatique avec Symfony

Une approche Domain-Driven Design pragmatique pour des projets Symfony existants, balançant pureté théorique et contraintes réelles.

## Principe Fondamental : Isolation via DTOs

**Règle d'or** : L'isolation des domaines se fait au niveau API via des DTOs, pas au niveau Doctrine.

❌ **DDD Pur** : Entités Domain sans annotations  
✅ **DDD Pragmatique** : Entités Domain avec annotations Doctrine (acceptable pour les performances)

## Architecture Multi-Bounded Contexts

```
src/BoundedContext/
├── VideoGamesRecords/          # Contexte principal
│   ├── Core/                   # Sous-domaine principal
│   ├── Badge/                  # Sous-domaine badges
│   ├── Proof/                  # Sous-domaine preuves
│   └── Team/                   # Sous-domaine équipes
├── Article/                    # Contexte articles
├── Forum/                      # Contexte forum
└── SharedKernel/               # Code partagé transversal
```

### Organisation par Contexte

Chaque contexte suit cette structure :

```
VideoGamesRecords/Core/
├── Domain/
│   ├── Entity/
│   │   └── Game.php                    # Entité avec annotations Doctrine (OK)
│   └── Repository/
│       └── GameRepositoryInterface.php # Interface métier
├── Application/
│   ├── DTO/                           # DTOs pour l'API
│   │   ├── Response/
│   │   │   ├── GameResponseDTO.php    # DTO principal
│   │   │   ├── SerieDTO.php          # DTO imbriqué
│   │   │   └── PlatformDTO.php       # DTO imbriqué
│   │   └── Request/
│   │       └── CreateGameDTO.php      # DTO de création
│   ├── Mapper/
│   │   └── GameMapper.php             # Entity → DTO
│   └── Service/
│       └── GameService.php            # Logique applicative
├── Infrastructure/
│   ├── Persistence/
│   │   └── DoctrineGameRepository.php # Implémentation Doctrine
│   └── ApiPlatform/
│       └── GameDataProvider.php       # Fournit DTOs à API Platform
└── Presentation/
    └── Api/
        └── Controller/
            └── GameController.php      # Controller API (si nécessaire)
```

## Pattern DTO Obligatoire

### 1. DTO de Réponse

```php
<?php
// Application/DTO/Response/GameResponseDTO.php

namespace App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Response;

class GameResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $summary,
        public readonly ?\DateTimeInterface $releaseDate,
        public readonly ?SerieDTO $serie,
        /** @var PlatformDTO[] */
        public readonly array $platforms,
    ) {}
}

class SerieDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
    ) {}
}

class PlatformDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $abbreviation,
    ) {}
}
```

### 2. Mapper Entity → DTO

```php
<?php
// Application/Mapper/GameMapper.php

namespace App\BoundedContext\VideoGamesRecords\Core\Application\Mapper;

use App\BoundedContext\VideoGamesRecords\Core\Domain\Entity\Game;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Response\GameResponseDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Response\SerieDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Response\PlatformDTO;

class GameMapper
{
    public function toGameResponseDTO(Game $game): GameResponseDTO
    {
        return new GameResponseDTO(
            id: $game->getId(),
            name: $game->getName(),
            slug: $game->getSlug(),
            summary: $game->getSummary(),
            releaseDate: $game->getReleaseDate(),
            serie: $game->getSerie() ? $this->toSerieDTO($game->getSerie()) : null,
            platforms: array_map(
                fn($platform) => $this->toPlatformDTO($platform),
                $game->getPlatforms()->toArray()
            )
        );
    }

    private function toSerieDTO($serie): SerieDTO
    {
        return new SerieDTO(
            id: $serie->getId(),
            name: $serie->getName(),
            slug: $serie->getSlug()
        );
    }

    private function toPlatformDTO($platform): PlatformDTO
    {
        return new PlatformDTO(
            id: $platform->getId(),
            name: $platform->getName(),
            abbreviation: $platform->getAbbreviation()
        );
    }
}
```

### 3. DataProvider API Platform

```php
<?php
// Infrastructure/ApiPlatform/GameDataProvider.php

namespace App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BoundedContext\VideoGamesRecords\Core\Application\DTO\Response\GameResponseDTO;
use App\BoundedContext\VideoGamesRecords\Core\Application\Mapper\GameMapper;
use App\BoundedContext\VideoGamesRecords\Core\Domain\Repository\GameRepositoryInterface;

class GameDataProvider implements ProviderInterface
{
    public function __construct(
        private GameRepositoryInterface $gameRepository,
        private GameMapper $gameMapper
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): GameResponseDTO
    {
        $game = $this->gameRepository->find($uriVariables['id']);
        
        if (!$game) {
            throw new NotFoundHttpException('Game not found');
        }

        return $this->gameMapper->toGameResponseDTO($game);
    }
}
```

### 4. Configuration API Platform sur DTO

```php
<?php
// Application/DTO/Response/GameResponseDTO.php

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\BoundedContext\VideoGamesRecords\Core\Infrastructure\ApiPlatform\GameDataProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/games/{id}',
            provider: GameDataProvider::class
        )
    ]
)]
class GameResponseDTO
{
    // ... construction identique
}
```

## Relations Inter-Contextes

### Relations Doctrine Autorisées

```php
<?php
// Domain/Entity/Game.php

namespace App\BoundedContext\VideoGamesRecords\Core\Domain\Entity;

use App\BoundedContext\VideoGamesRecords\Igdb\Domain\Entity\Platform; // Cross-context
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Game
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    // ✅ Relation cross-context autorisée
    #[ORM\ManyToMany(targetEntity: Platform::class)]
    #[ORM\JoinTable(name: 'vgr_game_platform')]
    private Collection $platforms;

    // ✅ Relation intra-context
    #[ORM\ManyToOne(targetEntity: Serie::class)]
    private ?Serie $serie = null;

    // ... getters/setters
}
```

### Pourquoi c'est acceptable

1. **Performances** : JOINs SQL efficaces
2. **Simplicité** : Pas de patterns complexes
3. **Migration progressive** : Cohabitation avec l'existant
4. **Isolation réelle** : Se fait via les DTOs, pas Doctrine

## Règles Strictes

### ✅ À Faire

```php
// ✅ DTO exposé via API Platform
#[ApiResource(provider: GameDataProvider::class)]
class GameResponseDTO {}

// ✅ Mapper pour transformation
class GameMapper {
    public function toGameResponseDTO(Game $game): GameResponseDTO {}
}

// ✅ Relations Doctrine cross-context
#[ORM\ManyToMany(targetEntity: Platform::class)]
private Collection $platforms;
```

### ❌ Interdictions

```php
// ❌ Entité Domain directement exposée
#[ApiResource]
#[ORM\Entity]
class Game {} // JAMAIS !

// ❌ Serialization Groups sur entités
#[Groups(['game:read'])]
#[ORM\Column]
private string $name; // NON !

// ❌ Controller retournant des entités
return $this->json($game); // INTERDIT !

// ❌ Exposition de données sensibles
public readonly string $internalStatus; // Risque sécurité
```

## Patterns à Favoriser

### 1. Service de Composition

```php
<?php
// Application/Service/GameService.php

class GameService
{
    public function getGameWithStats(int $gameId): GameWithStatsDTO
    {
        $game = $this->gameRepository->find($gameId);
        $stats = $this->statsService->getGameStats($gameId); // Autre contexte
        
        return $this->gameMapper->toGameWithStatsDTO($game, $stats);
    }
}
```

### 2. DataProvider Complexe

```php
<?php
// Infrastructure/ApiPlatform/GameListDataProvider.php

class GameListDataProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $games = $this->gameRepository->findWithFilters($context['filters'] ?? []);
        
        return array_map(
            fn(Game $game) => $this->gameMapper->toGameResponseDTO($game),
            $games
        );
    }
}
```

### 3. Validation Métier vs Technique

```php
<?php
// Domain/Entity/Game.php
class Game
{
    public function updateName(string $name): void
    {
        if (empty(trim($name))) {
            throw new DomainException('Game name cannot be empty');
        }
        $this->name = $name;
    }
}

// Application/DTO/Request/UpdateGameDTO.php
class UpdateGameDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;
}
```

## Migration Progressive

### 1. Stratégie de Migration

```php
// Ancien endpoint (à conserver temporairement)
#[Route('/api/legacy/games/{id}')]
public function legacyGame(Game $game): JsonResponse
{
    return $this->json($game, 200, [], ['groups' => ['game:read']]);
}

// Nouvel endpoint (avec DTO)
// API Platform automatique via GameResponseDTO
```

### 2. Tests de Régression

```php
<?php
// Tests/Api/GameApiTest.php

class GameApiTest extends ApiTestCase
{
    public function testLegacyEndpointStillWorks(): void
    {
        // Test ancien endpoint
        $this->client->request('GET', '/api/legacy/games/1');
        $this->assertResponseIsSuccessful();
    }

    public function testNewEndpointWithDTO(): void
    {
        // Test nouveau endpoint
        $this->client->request('GET', '/games/1');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'name' => 'Test Game',
            'platforms' => [
                ['name' => 'PlayStation 5']
            ]
        ]);
    }
}
```

## Shared Kernel Local

### Structure par Contexte

```
VideoGamesRecords/
├── SharedKernel/           # Partagé dans VideoGamesRecords seulement
│   ├── Domain/
│   │   └── ValueObject/
│   │       └── Slug.php
│   └── Infrastructure/
│       └── Service/
│           └── SlugGenerator.php
├── Core/
├── Badge/
└── Proof/

# Shared Kernel global pour tous les contextes
src/SharedKernel/
├── Domain/
│   └── Security/
└── Infrastructure/
```

## Exemple Concret d'Implémentation

### Jeu avec Série et Plateformes

```php
<?php
// Résultat API : GET /games/1
{
    "id": 1,
    "name": "The Legend of Zelda: Breath of the Wild",
    "slug": "the-legend-of-zelda-breath-of-the-wild",
    "summary": "Open-world adventure game...",
    "releaseDate": "2017-03-03T00:00:00+00:00",
    "serie": {
        "id": 5,
        "name": "The Legend of Zelda",
        "slug": "the-legend-of-zelda"
    },
    "platforms": [
        {
            "id": 20,
            "name": "Nintendo Switch",
            "abbreviation": "Switch"
        },
        {
            "id": 21,
            "name": "Wii U",
            "abbreviation": "WiiU"
        }
    ]
}
```

### Avantages de cette Approche

1. **Contrôle Total** : DTO définit exactement ce qui est exposé
2. **Sécurité** : Pas de fuite de données internes
3. **Évolutivité** : API stable même si entités changent
4. **Performance** : JOINs SQL optimisés via Doctrine
5. **Migration** : Cohabitation ancien/nouveau

### Points d'Attention

1. **Duplication** : DTOs peuvent ressembler aux entités
2. **Maintenance** : Synchroniser entité ↔ DTO lors des changements
3. **Mapping** : Complexité des mappers pour relations profondes
4. **Tests** : Tester à la fois entités et DTOs

## Commandes CLI Utiles

```bash
# Générer un DTO depuis une entité
bin/console make:dto GameResponseDTO --from-entity=Game

# Générer un mapper
bin/console make:mapper GameMapper --from=Game --to=GameResponseDTO

# Générer un DataProvider
bin/console make:api-platform:provider GameDataProvider --for-dto=GameResponseDTO
```

## Anti-Patterns à Éviter

### 1. Exposition Directe d'Entité

```php
// ❌ JAMAIS FAIRE CELA
#[ApiResource]
#[ORM\Entity]
class Game
{
    #[ORM\Column]
    private string $internalStatus; // Donnée sensible exposée !
}
```

### 2. Serialization Groups Complexes

```php
// ❌ Éviter - Difficile à maintenir
#[Groups(['game:read', 'game:list', 'admin:read'])]
#[ORM\Column]
private string $name;
```

### 3. Controllers Trop Couplés

```php
// ❌ Controller trop couplé au domain
public function game(Game $game): JsonResponse
{
    // Logique métier dans le controller
    $game->calculateScore();
    $game->updateRanking();
    
    return $this->json($game); // + exposition directe
}
```

Cette approche pragmatique permet de bénéficier des avantages du DDD tout en restant pragmatique pour des projets existants avec des contraintes de performance et de migration.