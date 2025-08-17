# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Megio Core is a PHP 8.3+ framework for building web applications, APIs, and console applications. It combines Symfony components with Nette DI/Latte and includes Doctrine ORM for database operations.

**Key Technologies:**
- PHP 8.3+ with strict types
- Symfony HTTP Kernel, Console, Routing, EventDispatcher
- Nette DI Container, Latte templating, Security
- Doctrine ORM with PostgreSQL
- PHPStan (level 8) for static analysis
- Pest PHP for testing
- Docker with docker-compose for development

## Development Commands

### Docker Environment
```bash
# Start development environment
docker-compose up -d --build

# Access application container shell
make sh

# Setup test environment (clean start)
make test-setup
```

### Testing & Quality Assurance
```bash
# Run all tests and analysis
make test
# Equivalent to: docker compose exec app composer analyse

# Run specific test file
make test-single FILE=tests/Collection/UpdateRowsTest.php

# Individual commands (run inside container):
composer test          # Run Pest tests
composer phpstan        # Static analysis
composer neon          # Validate Neon config files
composer latte         # Validate Latte templates
composer schema        # Validate Doctrine schema
```

### Database Operations
```bash
# Generate migration from entity changes
bin/console migration:diff --no-interaction

# Run pending migrations
bin/console migration:migrate --no-interaction

# Update auth resources
bin/console app:auth:resources:update

# Create admin user
bin/console admin admin@test.cz Test1234
```

## Architecture Overview

### Bootstrap & DI Container
- `src/Bootstrap.php`: Main application bootstrap that configures DI container, environment, and Tracy debugger
- Configuration files in `config/`: Neon format for services and extensions
- Extensions system allows modular functionality registration

### HTTP Layer
- `src/Http/Kernel/App.php`: Creates Symfony HttpKernel with custom argument resolvers
- `router/app.php`: Route definitions using Symfony routing
- Controllers resolve dependencies through Nette DI container
- Request/Response handling via Symfony components

### Database Layer
- `app/EntityManager.php`: Extended Doctrine EntityManager with typed repository getters
- Entities in `app/*/Database/Entity/`
- Repositories in `app/*/Database/Repository/`
- Migrations in `migrations/` directory

### Core Modules (src/)
- `Collection/`: Data collection builders (Read/Write/Search)
- `Database/`: Doctrine extensions and entity management
- `Security/`: JWT authentication and authorization
- `Console/`: Symfony Console commands
- `Http/`: HTTP kernel, resolvers, routing
- `Storage/`: File storage abstraction
- `Queue/`: Background job processing
- `Mailer/`: Email functionality

### Application Structure (app/)
- Modular organization by feature (Admin/, User/, Hooray/, Article/)
- Each module contains Database/ subdirectory with entities/repositories
- `QueueWorker.php`: Background job processing

### Testing
- Uses Pest PHP testing framework
- `tests/MegioTestCase.php`: Base test case with common setup
- Tests organized by module in `tests/` directory
- Configuration in `tests/Pest.php`

## Key Configuration Files

- `config/app.neon`: Main service configuration
- `phpstan.neon`: Static analysis rules (level 8)
- `phpunit.xml`: Test configuration
- `docker-compose.yml`: Development environment
- `.env`: Environment variables (copy from `.env.example`)

## Development Workflow

1. Modify entities and run `migration:diff` to generate migrations
2. Run `make test` to validate all code quality checks
3. Use `make sh` to access container for debugging
4. Tests run in isolated environment via `make test-setup`

## Important Notes

- All PHP files use `declare(strict_types=1)`
- PHPStan level 8 enforcement - fix all type issues
- Pest test framework with custom expectations
- Docker-based development with PostgreSQL
- Tracy debugger with custom editor mapping support