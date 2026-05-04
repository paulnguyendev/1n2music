## ADDED Requirements

### Requirement: Entrypoint script for Laravel initialization
The docker-entrypoint.sh script SHALL automatically initialize the Laravel application on container startup including waiting for database, running migrations, and setting permissions.

#### Scenario: Script waits for database availability
- **WHEN** PHP container starts before MySQL is ready
- **THEN** the entrypoint script waits for MySQL to accept connections before proceeding

#### Scenario: Script runs database migrations
- **WHEN** container starts with pending migrations
- **THEN** the entrypoint script runs `php artisan migrate --force` to apply migrations

#### Scenario: Script sets correct file permissions
- **WHEN** container starts
- **THEN** the entrypoint script sets write permissions on `storage` and `bootstrap/cache` directories

### Requirement: Laravel optimization commands
The entrypoint script SHALL run Laravel optimization commands including config cache, route cache, and view cache.

#### Scenario: Configuration is cached
- **WHEN** container starts in production mode
- **THEN** the script runs `php artisan config:cache` to optimize configuration loading

#### Scenario: Routes are cached
- **WHEN** container starts in production mode
- **THEN** the script runs `php artisan route:cache` to optimize route loading

#### Scenario: Views are cleared
- **WHEN** container starts
- **THEN** the script runs `php artisan view:clear` to clear compiled views (as per application requirement)

### Requirement: Environment-specific behavior
The entrypoint script SHALL detect the APP_ENV variable and adjust behavior for development vs production environments.

#### Scenario: Development mode skips optimization
- **WHEN** APP_ENV is set to `local` or `development`
- **THEN** the script skips config:cache and route:cache commands

#### Scenario: Production mode enables all optimizations
- **WHEN** APP_ENV is set to `production`
- **THEN** the script runs all optimization commands

### Requirement: Database connection retry logic
The entrypoint script SHALL implement retry logic with exponential backoff when waiting for database availability.

#### Scenario: Script retries database connection
- **WHEN** MySQL is not immediately available
- **THEN** the script retries connection up to 30 times with 1-second intervals

#### Scenario: Script fails gracefully on timeout
- **WHEN** database is not available after 30 retries
- **THEN** the script exits with error message and non-zero exit code

### Requirement: Storage directory initialization
The entrypoint script SHALL ensure all required Laravel storage subdirectories exist with correct permissions.

#### Scenario: Storage directories are created
- **WHEN** container starts with missing storage directories
- **THEN** the script creates `storage/framework/cache`, `storage/framework/sessions`, `storage/framework/views`, and `storage/logs`

#### Scenario: Storage directories have write permissions
- **WHEN** storage directories are created
- **THEN** all directories have 775 permissions and are owned by www-data

### Requirement: Application key validation
The entrypoint script SHALL validate that APP_KEY is set before starting the application.

#### Scenario: Script validates APP_KEY exists
- **WHEN** container starts without APP_KEY in .env
- **THEN** the script generates a new key using `php artisan key:generate`

#### Scenario: Script continues with existing APP_KEY
- **WHEN** container starts with APP_KEY already set
- **THEN** the script skips key generation and continues

### Requirement: PHP-FPM process management
The entrypoint script SHALL start PHP-FPM in foreground mode to keep the container running.

#### Scenario: PHP-FPM runs in foreground
- **WHEN** entrypoint script completes initialization
- **THEN** PHP-FPM starts with `-F` flag to run in foreground

#### Scenario: Container stops when PHP-FPM exits
- **WHEN** PHP-FPM process terminates
- **THEN** the container exits with the same exit code

### Requirement: Composer autoload optimization
The entrypoint script SHALL run `composer dump-autoload --optimize` to ensure optimized class loading.

#### Scenario: Autoloader is optimized on startup
- **WHEN** container starts
- **THEN** the script runs `composer dump-autoload --optimize` to rebuild optimized autoloader
