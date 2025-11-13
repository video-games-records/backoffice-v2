# Makefile to automate Symfony project testing and development

.PHONY: help test test-unit test-integration test-functional test-coverage test-dox test-user test-user-api test-user-unit test-user-integration install setup db-setup db-reset lint fix serve clear cache

# Default configuration
PHP_BIN := php
COMPOSER_BIN := composer
PHPUNIT_BIN := php ./vendor/bin/simple-phpunit
CONSOLE_BIN := php bin/console

# Colors for output
GREEN := \033[32m
YELLOW := \033[33m
RED := \033[31m
CYAN := \033[36m
RESET := \033[0m

help: ## Show this help message
	@echo "$(CYAN)Available commands:$(RESET)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "$(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'

##@ Installation and Setup
install: ## Install dependencies
	@echo "$(GREEN)Installing dependencies...$(RESET)"
	$(COMPOSER_BIN) install

setup: install db-setup ## Full project setup (install + database)
	@echo "$(GREEN)Project setup complete!$(RESET)"

##@ Database
db-local: ## Create local database with fixtures
	@echo "$(YELLOW)Setting up local database...$(RESET)"
	$(CONSOLE_BIN) doctrine:database:drop --force --if-exists
	$(CONSOLE_BIN) doctrine:database:create
	$(CONSOLE_BIN) doctrine:schema:update --force
	$(CONSOLE_BIN) doctrine:fixtures:load --no-interaction

db-test: ## Create test database with fixtures
	@echo "$(YELLOW)Setting up test database...$(RESET)"
	$(CONSOLE_BIN) doctrine:database:drop --force --env=test --if-exists
	$(CONSOLE_BIN) doctrine:database:create --env=test
	$(CONSOLE_BIN) doctrine:schema:update --force --env=test
	$(CONSOLE_BIN) doctrine:fixtures:load --no-interaction --env=test
	@echo "$(GREEN)Test database ready with fixtures!$(RESET)"

db-setup: db-test ## Alias for db-test (backward compatibility)

db-reset: db-test ## Alias for db-test (backward compatibility)

##@ Testing
test: ## Run all tests
	@echo "$(GREEN)Running all tests...$(RESET)"
	$(PHPUNIT_BIN)

test-unit: ## Run unit tests only
	@echo "$(GREEN)Running unit tests...$(RESET)"
	$(PHPUNIT_BIN) --testsuite=Unit

test-integration: ## Run integration tests only
	@echo "$(GREEN)Running integration tests...$(RESET)"
	$(PHPUNIT_BIN) --testsuite=Integration

test-functional: ## Run functional tests only
	@echo "$(GREEN)Running functional tests...$(RESET)"
	$(PHPUNIT_BIN) --testsuite=Functional

test-coverage: ## Run tests with HTML coverage report
	@echo "$(GREEN)Running tests with coverage...$(RESET)"
	$(PHPUNIT_BIN) --coverage-html var/coverage
	@echo "$(CYAN)Coverage report available at: var/coverage/index.html$(RESET)"

test-coverage-text: ## Run tests with text coverage report
	@echo "$(GREEN)Running tests with text coverage...$(RESET)"
	$(PHPUNIT_BIN) --coverage-text

test-dox: ## Run tests with documentation format
	@echo "$(GREEN)Running tests in documentation format...$(RESET)"
	$(PHPUNIT_BIN) --testdox

test-watch: ## Watch tests (requires 'watch' command)
	@echo "$(GREEN)Watching tests... (Ctrl+C to stop)$(RESET)"
	watch -n 2 'make test'

test-specific: ## Run specific test file (usage: make test-specific FILE=tests/Unit/KernelTest.php)
	@echo "$(GREEN)Running specific test: $(FILE)$(RESET)"
	$(PHPUNIT_BIN) $(FILE)

test-user: ## Run all User BoundedContext tests
	@echo "$(GREEN)Running User BoundedContext tests...$(RESET)"
	$(PHPUNIT_BIN) tests/BoundedContext/User/ --testdox

test-user-api: ## Run User API tests only
	@echo "$(GREEN)Running User API tests...$(RESET)"
	@echo "$(YELLOW)Clearing rate limiter cache...$(RESET)"
	$(CONSOLE_BIN) cache:pool:clear cache.rate_limiter --env=test
	$(PHPUNIT_BIN) tests/BoundedContext/User/Functional/Api/ --testdox

test-user-unit: ## Run User unit tests only
	@echo "$(GREEN)Running User unit tests...$(RESET)"
	$(PHPUNIT_BIN) tests/BoundedContext/User/Unit/ --testdox --exclude-group integration

test-user-integration: ## Run User integration tests only
	@echo "$(GREEN)Running User integration tests...$(RESET)"
	$(PHPUNIT_BIN) tests/BoundedContext/User/Integration/ --testdox

##@ Code Quality
phpstan: ## Run PHPStan static analysis
	@echo "$(GREEN)Running PHPStan analysis...$(RESET)"
	$(PHP_BIN) vendor/bin/phpstan analyse --memory-limit=1G

phpcs: ## Run PHP_CodeSniffer code style check
	@echo "$(GREEN)Running PHP_CodeSniffer...$(RESET)"
	$(PHP_BIN) vendor/bin/phpcs

phpcs-fix: ## Fix code style issues with PHP_CodeSniffer
	@echo "$(GREEN)Fixing code style with PHPCBF...$(RESET)"
	$(PHP_BIN) vendor/bin/phpcbf

lint: phpstan phpcs ## Run all code quality checks (PHPStan + PHPCS)
	@echo "$(GREEN)All code quality checks completed!$(RESET)"

fix: phpcs-fix ## Alias for phpcs-fix

# Legacy compatibility
lint-phpstan: phpstan ## Legacy alias for phpstan
lint-phpcs: phpcs ## Legacy alias for phpcs

##@ Development
serve: ## Start development server
	@echo "$(GREEN)Starting development server at http://127.0.0.1:8000$(RESET)"
	symfony server:start

clear: ## Clear all caches
	@echo "$(YELLOW)Clearing caches...$(RESET)"
	$(CONSOLE_BIN) cache:clear
	$(CONSOLE_BIN) cache:clear --env=test
	@echo "$(GREEN)Caches cleared!$(RESET)"

cache: ## Warm up cache
	@echo "$(YELLOW)Warming up cache...$(RESET)"
	$(CONSOLE_BIN) cache:warmup
	@echo "$(GREEN)Cache warmed up!$(RESET)"

##@ Security and Maintenance
security-check: ## Check for security vulnerabilities
	@echo "$(GREEN)Checking for security vulnerabilities...$(RESET)"
	$(COMPOSER_BIN) audit

update: ## Update dependencies
	@echo "$(GREEN)Updating dependencies...$(RESET)"
	$(COMPOSER_BIN) update

##@ API Platform
api-docs: ## Generate API documentation
	@echo "$(GREEN)API documentation available at: http://127.0.0.1:8000/api$(RESET)"

##@ JWT
jwt-generate-keys: ## Generate JWT keys
	@echo "$(YELLOW)Generating JWT keys...$(RESET)"
	mkdir -p config/jwt
	openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
	openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
	@echo "$(GREEN)JWT keys generated!$(RESET)"

##@ Utilities
logs: ## Show logs
	@echo "$(GREEN)Showing logs...$(RESET)"
	tail -f var/log/dev.log

logs-test: ## Show test logs
	@echo "$(GREEN)Showing test logs...$(RESET)"
	tail -f var/log/test.log

clean: ## Clean generated files
	@echo "$(YELLOW)Cleaning generated files...$(RESET)"
	rm -rf var/cache/*
	rm -rf var/log/*
	rm -rf var/coverage/*
	@echo "$(GREEN)Cleanup complete!$(RESET)"

##@ Project Info
info: ## Show project information
	@echo "$(CYAN)Project Information:$(RESET)"
	@echo "PHP Version: $(shell $(PHP_BIN) -v | head -n 1)"
	@echo "Composer Version: $(shell $(COMPOSER_BIN) --version)"
	@echo "Symfony Version: $(shell $(CONSOLE_BIN) --version)"
	@echo "PHPUnit Version: $(shell $(PHPUNIT_BIN) --version)"


##@ Quick Commands (shortcuts)
t: test ## Shortcut for test
tu: test-unit ## Shortcut for test-unit
ti: test-integration ## Shortcut for test-integration
tf: test-functional ## Shortcut for test-functional
tc: test-coverage ## Shortcut for test-coverage
tu-user: test-user ## Shortcut for test-user
tu-api: test-user-api ## Shortcut for test-user-api
l: lint ## Shortcut for lint
f: fix ## Shortcut for fix
s: serve ## Shortcut for serve
c: clear ## Shortcut for clear
