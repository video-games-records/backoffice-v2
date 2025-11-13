# Guide d'utilisation UserFactory et AdminUserStory

Ce guide explique comment utiliser les Factory et Story pour créer des utilisateurs dans les tests.

## Organisation DDD

Les Factory et Story sont organisées par BoundedContext :

- **UserFactory** : `src/BoundedContext/User/Tests/Factory/UserFactory.php`
- **AdminUserStory** : `src/BoundedContext/User/Tests/Story/AdminUserStory.php`

## UserFactory

Le `UserFactory` utilise Zenstruck Foundry pour créer facilement des utilisateurs de test avec différents rôles et configurations.

### Utilisation de base

```php
use App\BoundedContext\User\Tests\Factory\UserFactory;

// Utilisateur basique
$user = UserFactory::new()->create();

// Utilisateur avec email/username spécifiques
$user = UserFactory::new()
    ->withCredentials('test@example.com', 'testuser', 'password')
    ->create();
```

### Rôles prédéfinis

```php
// Utilisateur normal
$user = UserFactory::new()->asUser()->create();

// Admin
$admin = UserFactory::new()->asAdmin()->create();

// Super Admin
$superAdmin = UserFactory::new()->asSuperAdmin()->create();

// Rôles personnalisés
$user = UserFactory::new()
    ->withRoles(['ROLE_CUSTOM', 'ROLE_OTHER'])
    ->create();
```

### Autres options

```php
// Utilisateur désactivé
$user = UserFactory::new()->disabled()->create();

// Créer plusieurs utilisateurs
UserFactory::new()->many(5)->create();
```

## AdminUserStory

L'`AdminUserStory` crée un ensemble cohérent d'utilisateurs pour les tests qui nécessitent des données prévisibles.

### Utilisation

```php
use App\BoundedContext\User\Tests\Story\AdminUserStory;

// Dans votre test, chargez la story
AdminUserStory::load();

// Récupérez les utilisateurs prédéfinis
$admin = AdminUserStory::adminUser();      // Super admin avec admin/admin
$moderator = AdminUserStory::moderatorUser(); // Admin avec moderator/moderator
$user = AdminUserStory::regularUser();     // Utilisateur normal avec user/user
```

### Utilisateurs créés par la Story

1. **admin** (ID: 1)
   - Email: admin@local.fr
   - Username: admin
   - Password: admin
   - Rôles: ROLE_SUPER_ADMIN

2. **moderator**
   - Email: moderator@local.fr  
   - Username: moderator
   - Password: moderator
   - Rôles: ROLE_ADMIN

3. **user** (ID: 2)
   - Email: user@local.fr
   - Username: user
   - Password: user
   - Rôles: []

4. **3 utilisateurs aléatoires** pour les tests

## Exemples d'utilisation dans les tests

### Test avec authentification session (WebTestCase)

```php
use App\BoundedContext\User\Tests\Factory\UserFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class MyControllerTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    public function testWithAdmin(): void
    {
        $client = static::createClient();
        
        // Option 1: Utiliser la Story
        AdminUserStory::load();
        $admin = AdminUserStory::adminUser();
        $client->loginUser($admin->_real());
        
        // Option 2: Créer un admin à la volée
        $admin = UserFactory::new()->asSuperAdmin()->create();
        $client->loginUser($admin->_real());
        
        $client->request('GET', '/admin/dashboard');
        $this->assertResponseIsSuccessful();
    }
}
```

### Test API avec JWT (ApiTestCase)

```php
use App\BoundedContext\User\Tests\Factory\UserFactory;
use App\BoundedContext\User\Tests\Story\AdminUserStory;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class MyApiTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    public function testApiWithAuth(): void
    {
        // Charger les utilisateurs de base
        AdminUserStory::load();
        
        // Login pour récupérer le token
        $response = $this->createClient()->request('POST', '/api/login_check', [
            'json' => [
                'username' => 'admin',
                'password' => 'admin',
            ]
        ]);
        
        $token = $response->toArray()['token'];
        
        // Utiliser le token pour les requêtes protégées
        $this->createClient()->request('GET', '/api/users', [
            'auth_bearer' => $token
        ]);
        
        $this->assertResponseIsSuccessful();
    }
}
```

### Test avec utilisateur spécifique

```php
public function testWithSpecificUser(): void
{
    $client = static::createClient();
    
    // Créer un utilisateur avec des données spécifiques
    $user = UserFactory::new()
        ->withCredentials('specific@test.com', 'specific')
        ->withRoles(['ROLE_MODERATOR'])
        ->create();
    
    $client->loginUser($user->_real());
    
    $client->request('GET', '/moderate');
    $this->assertResponseIsSuccessful();
}
```

## Bonnes pratiques

1. **Utilisez `ResetDatabase`** : Pour garantir l'isolation entre tests
2. **Utilisez `AdminUserStory`** : Pour des données cohérentes entre tests
3. **Créez à la volée** : Pour des besoins spécifiques de test
4. **Appelez `_real()`** : Pour obtenir l'entité Doctrine réelle depuis le proxy Foundry

## Avantages

- ✅ **Isolation des tests** : Chaque test a sa propre base de données
- ✅ **Données cohérentes** : La Story garantit des données prévisibles  
- ✅ **Flexibilité** : Factory permet de créer des utilisateurs personnalisés
- ✅ **Performance** : Foundry optimise la création d'entités
- ✅ **Lisibilité** : Code de test plus expressif et maintenant