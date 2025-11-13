# Symfony Skeleton Project

A modern Symfony project with API Platform, Sonata Admin, and multilingual article management.

## 🚀 Features

- **RESTful API & GraphQL** with API Platform
- **Admin interface** with Sonata Admin
- **Multilingual article management** (French, English, Spanish, German)
- **JWT authentication** with refresh tokens
- **Data auditing** with DH Auditor Bundle
- **Message monitoring** with Messenger Monitor
- **Built-in log viewer**
- **Automated testing** with PHPUnit

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Node.js and npm (for assets)
- Database (PostgreSQL, MySQL, or SQLite)

## 🛠️ Installation

### 1. Clone the project

```bash
git clone <repo-url>
cd symfony-skeleton
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Configuration

Copy the `.env` file and configure your environment variables:

```bash
cp .env .env.local
```

Important variables to configure in `.env.local`:

```env
# Database
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/app"
DATABASE_URL_AUDIT="postgresql://user:password@127.0.0.1:5432/app_audit"

# JWT
JWT_PASSPHRASE=your_jwt_passphrase

# Mailer
MAILER_DSN=smtp://localhost:1025
MAILER_ENVELOPE_SENDER=noreply@example.com

# CORS
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
```

### 4. Generate JWT keys

```bash
php bin/console lexik:jwt:generate-keypair
```

### 5. Create the database

```bash
make db-local
```

```bash
php bin/console audit:schema:update --force
```

## 🏃‍♂️ Quick Start

### Development

```bash
# Start Symfony server
symfony server:start
```

The application will be available at `http://localhost:8000`

### Default accounts

After loading fixtures:

- **Admin**: `admin@local.fr` / `admin`
- **User**: `user@local.fr` / `user`

## 📚 Usage

### Administration

Access the admin interface: `http://localhost:8000/admin`

Available features:
- User management
- Multilingual article management
- Message monitoring
- Log viewer
- Data auditing

### API

#### Documentation

- **Swagger UI**: `http://localhost:8000/api/docs`
- **GraphQL Playground**: `http://localhost:8000/api/graphql`
- **GraphiQL**: `http://localhost:8000/api/graphiql`

#### Authentication

```bash
# Get a JWT token
curl -X POST http://localhost:8000/api/login_check \
  -H "Content-Type: application/json" \
  -d '{"username": "admin@local.fr", "password": "admin"}'

# Use the token
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8000/api/articles
```

#### Main endpoints

- `GET /api/articles` - List articles
- `POST /api/articles` - Create an article
- `GET /api/articles/{id}` - Article details
- `PUT /api/articles/{id}` - Update an article
- `DELETE /api/articles/{id}` - Delete an article

### Monitoring

- **Messages**: `http://localhost:8000/admin/messenger`
- **Logs**: `http://localhost:8000/admin/logs`
- **Audit**: `http://localhost:8000/audit`

## 🧪 Testing

```bash
# Create test database
make db-test

# Run all tests
make test
```

## 📁 Project Structure

```
├── config/               # Symfony configuration
│   ├── packages/         # Bundle configuration
│   └── routes/           # Route configuration
├── public/               # Web entry point
├── src/
│   ├── Controller/       # Controllers
│   ├── Entity/           # Doctrine entities
│   ├── Repository/       # Repositories
│   └── DataFixtures/     # Test fixtures
├── templates/            # Twig templates
├── tests/                # Tests
└── var/                  # Cache, logs, etc.
```

## 🌐 Internationalization

The project supports multiple languages:
- French (fr) - default language
- English (en)
- Spanish (es)
- German (de)

### Adding a translation

1. Create translation files in `translations/`
2. Add the locale in `config/packages/projet_normandie_article.yaml`
3. Update `a2lix_translation_form.yaml` configuration

## 🔐 Security

### Roles

- `ROLE_USER` - Standard user
- `ROLE_ADMIN` - Administrator
- `ROLE_SUPER_ADMIN` - Super administrator
- `ROLE_MESSENGER_MONITOR` - Message monitoring access

### Firewalls

- `/admin/*` - Form-based authentication
- `/api/*` - JWT authentication
- `/audit/*` - Admin-restricted access

## 📊 Monitoring and Logs

### Available logs

- **Application**: General application logs
- **Doctrine**: SQL query logs
- **Deprecation**: Deprecation warnings

### Log viewer

Access logs via admin interface: `/admin/logs`

Features:
- Filter by log type
- Search within logs
- Download log files
- Real-time updates

### Code standards

This project uses:
- PSR-12 for code style
- PHPStan for static analysis
- Run `composer run lint` before committing

## 📝 Used Bundles

- **Symfony** 7.3 - Main framework
- **API Platform** 4.1 - REST and GraphQL API
- **Sonata Admin** 4.36 - Admin interface
- **Doctrine ORM** 3.3 - ORM
- **Lexik JWT** - JWT authentication
- **Stof Doctrine Extensions** - Doctrine extensions (Timestampable, Sluggable)
- **DH Auditor** - Data auditing
- **Zenstruck Messenger Monitor** - Message monitoring

## 📄 License

This project is under proprietary license.

## 📞 Support

For any questions or issues:
1. Check Symfony documentation
2. Review existing issues
3. Create a new issue if necessary