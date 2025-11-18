# Commandes d'Import IGDB

Ce guide documente toutes les commandes disponibles pour importer les données depuis l'API IGDB dans le contexte VideoGamesRecords.Igdb.

## Vue d'ensemble

Les commandes d'import IGDB permettent de synchroniser les données de jeux vidéo depuis l'[Internet Game Database (IGDB)](https://www.igdb.com/) vers la base de données locale. Toutes les commandes respectent l'architecture DDD et utilisent le service `IgdbImportService`.

## Configuration Préalable

### Variables d'environnement

Configurez vos credentials IGDB dans le fichier `.env` :

```bash
IGDB_CLIENT_ID=votre_client_id
IGDB_CLIENT_SECRET=votre_client_secret
```

### Configuration des services

Les services sont automatiquement configurés dans `config/services/igdb.yaml`.

## Commandes Disponibles

### 1. Import des Types de Plateformes

```bash
php bin/console igdb:import:platform-types [options]
```

**Description :** Importe tous les types de plateformes IGDB (Console, PC, Arcade, etc.).

**Options :**
- `--limit=50` : Limite le nombre de types à importer (défaut : 50)

**Exemple :**
```bash
# Import de tous les types avec limite par défaut
php bin/console igdb:import:platform-types

# Import avec limite personnalisée
php bin/console igdb:import:platform-types --limit=20
```

**Entités créées :** `PlatformType`

---

### 2. Import des Logos de Plateformes

```bash
php bin/console igdb:import:platform-logos [options]
```

**Description :** Importe les logos de toutes les plateformes avec leurs métadonnées d'images.

**Options :**
- `--limit=100` : Limite le nombre de logos à importer (défaut : 100)

**Exemple :**
```bash
# Import de tous les logos
php bin/console igdb:import:platform-logos

# Import avec limite spécifique
php bin/console igdb:import:platform-logos --limit=50
```

**Entités créées :** `PlatformLogo`

---

### 3. Import des Plateformes

```bash
php bin/console igdb:import:platforms [options]
```

**Description :** Importe toutes les plateformes de jeu avec leurs relations aux types et logos.

**Options :**
- `--limit=100` : Limite le nombre de plateformes à importer (défaut : 100)
- `--offset=0` : Décalage pour l'import par batch (défaut : 0)

**Exemple :**
```bash
# Import de toutes les plateformes
php bin/console igdb:import:platforms

# Import par batch de 50 plateformes à partir de la 100ème
php bin/console igdb:import:platforms --limit=50 --offset=100
```

**Entités créées :** `Platform`

**Prérequis :** Types de plateformes et logos doivent être importés au préalable.

---

### 4. Import des Genres

```bash
php bin/console igdb:import:genres [options]
```

**Description :** Importe tous les genres de jeux depuis IGDB.

**Options :**
- `--limit=50` : Limite le nombre de genres à importer (défaut : 50)

**Exemple :**
```bash
# Import de tous les genres
php bin/console igdb:import:genres

# Import avec limite personnalisée
php bin/console igdb:import:genres --limit=30
```

**Entités créées :** `Genre`

---

### 5. Import des Jeux

```bash
php bin/console igdb:import:games [options]
```

**Description :** Importe les jeux vidéo avec toutes leurs métadonnées et relations.

**Options :**
- `--limit=100` : Limite le nombre de jeux à importer (défaut : 100)
- `--offset=0` : Décalage pour l'import par batch (défaut : 0)

**Exemple :**
```bash
# Import de 100 premiers jeux
php bin/console igdb:import:games

# Import par batch de 200 jeux à partir du 500ème
php bin/console igdb:import:games --limit=200 --offset=500
```

**Entités créées :** `Game`

**Prérequis :** Genres et plateformes doivent être importés au préalable.

**Fonctionnalités :**
- Support des jeux parents/enfants (versions, extensions)
- Import des dates de sortie et métadonnées
- Gestion des relations avec genres et plateformes
- Logging détaillé des erreurs d'import

---

### 6. Recherche et Import Sélectif

```bash
php bin/console igdb:search-import:games <nom_du_jeu> [options]
```

**Description :** Recherche et importe des jeux spécifiques par nom et critères.

**Arguments :**
- `<nom_du_jeu>` : Nom du jeu à rechercher (obligatoire)

**Options :**
- `-p, --platform=PLATFORM` : ID(s) de plateforme pour filtrer (multiple)
- `-l, --limit=10` : Nombre maximum de résultats à traiter (défaut : 10)
- `-x, --exact-match` : N'importer que les jeux avec correspondance exacte du nom
- `-d, --dry-run` : Afficher ce qui serait importé sans effectuer l'import

**Exemples :**
```bash
# Recherche simple
php bin/console igdb:search-import:games "The Legend of Zelda"

# Recherche avec filtrage par plateforme
php bin/console igdb:search-import:games "Mario" --platform=7 --platform=41

# Test sans import (dry-run)
php bin/console igdb:search-import:games "Final Fantasy" --dry-run

# Recherche avec correspondance exacte
php bin/console igdb:search-import:games "Minecraft" --exact-match --limit=5
```

**Fonctionnalités :**
- Recherche interactive par nom de jeu
- Filtrage par plateforme(s)
- Prévisualisation des résultats
- Import sélectif avec confirmation
- Mode dry-run pour tester les requêtes

## Ordre d'Exécution Recommandé

Les commandes doivent être exécutées dans l'ordre suivant pour respecter les dépendances entre entités :

```bash
# 1. Types de plateformes (dépendance de base)
php bin/console igdb:import:platform-types

# 2. Logos de plateformes
php bin/console igdb:import:platform-logos

# 3. Plateformes (dépend des types et logos)
php bin/console igdb:import:platforms

# 4. Genres
php bin/console igdb:import:genres

# 5. Jeux (dépend des genres et plateformes)
php bin/console igdb:import:games

# 6. Recherche et import spécifique (optionnel)
php bin/console igdb:search-import:games "nom du jeu"
```

## Gestion des Données

### Stratégie d'Import

- **Insertion :** Nouvelles entités non présentes en base
- **Mise à jour :** Entités existantes avec données plus récentes (basé sur `updated_at`)
- **Ignoré :** Entités existantes sans changement

### Gestion des Erreurs

Toutes les commandes incluent :
- **Logging détaillé** des erreurs d'API
- **Gestion des timeouts** et limitations de taux IGDB
- **Retry logic** pour les échecs temporaires
- **Rapports de résultats** (inséré/mis à jour/ignoré)

### Performance

- **Pagination automatique** pour les gros datasets
- **Import par batch** configurable via les options
- **Optimisation des requêtes** avec joins appropriés

## Bonnes Pratiques

### 1. Import Initial

Pour un import initial complet :

```bash
# Script d'import complet
#!/bin/bash
echo "Import des types de plateformes..."
php bin/console igdb:import:platform-types --limit=100

echo "Import des logos de plateformes..."
php bin/console igdb:import:platform-logos --limit=200

echo "Import des plateformes..."
php bin/console igdb:import:platforms --limit=200

echo "Import des genres..."
php bin/console igdb:import:genres --limit=100

echo "Import des jeux..."
php bin/console igdb:import:games --limit=1000

echo "Import terminé !"
```

### 2. Import Incrémental

Pour maintenir les données à jour :

```bash
# Mise à jour hebdomadaire (cron)
0 2 * * 0 cd /path/to/project && php bin/console igdb:import:games --limit=500
```

### 3. Import Spécifique

Pour ajouter des jeux particuliers :

```bash
# Recherche et ajout de jeux spécifiques
php bin/console igdb:search-import:games "Call of Duty" --platform=7
php bin/console igdb:search-import:games "FIFA 2024" --exact-match
```

### 4. Surveillance

Surveillez les logs d'import dans :
- Logs Symfony (`var/log/`)
- Output des commandes pour les statistiques d'import
- Interfaces admin Sonata pour vérifier les données importées

## Dépannage

### Problèmes Courants

1. **Erreur de credentials IGDB**
   ```
   Vérifiez vos variables IGDB_CLIENT_ID et IGDB_CLIENT_SECRET
   ```

2. **Timeout API**
   ```
   Réduisez les limites d'import (--limit) pour éviter les timeouts
   ```

3. **Dépendances manquantes**
   ```
   Respectez l'ordre d'import : PlatformTypes → Platforms → Games
   ```

4. **Mémoire insuffisante**
   ```
   Utilisez les options --limit et --offset pour traiter par batch
   ```

### Logs et Debugging

Les commandes génèrent des logs détaillés dans :
- Console (sortie standard)
- Logs Symfony (`var/log/dev.log` ou `var/log/prod.log`)

Pour un debugging plus détaillé, utilisez :
```bash
php bin/console igdb:import:games --limit=10 -vvv
```

## Interfaces Admin

Toutes les entités importées sont accessibles via l'interface Sonata Admin :

- **Jeux :** `/admin/igdb/game/list`
- **Genres :** `/admin/igdb/genre/list`
- **Plateformes :** `/admin/igdb/platform/list`
- **Types de plateformes :** `/admin/igdb/platform-type/list`
- **Logos :** `/admin/igdb/platform-logo/list`

**Caractéristiques des interfaces :**
- **Lecture seule** (pas de modification manuelle)
- **Filtrage avancé** et recherche
- **Export CSV/Excel** (si configuré)
- **Affichage des relations** entre entités

## Sécurité

- **Credentials protégés :** Ne jamais exposer `IGDB_CLIENT_ID` et `IGDB_CLIENT_SECRET`
- **Interfaces en lecture seule :** Aucune modification manuelle des données IGDB
- **Validation des données :** Toutes les données sont validées avant persistence

## Support et Maintenance

Pour toute question ou problème :
1. Consultez les logs d'erreur
2. Vérifiez la documentation IGDB API
3. Testez avec l'option `--dry-run` pour diagnostiquer
4. Utilisez des imports par petits batch en cas de problème

---

**Dernière mise à jour :** {{current_date}}
**Version du contexte :** 1.0.0