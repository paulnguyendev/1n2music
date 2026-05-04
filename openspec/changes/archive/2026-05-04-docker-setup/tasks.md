## 1. Project Structure Setup

- [x] 1.1 Create `docker/nginx` directory for Nginx configuration files
- [x] 1.2 Create `.dockerignore` file in project root

## 2. Dockerfile Creation

- [x] 2.1 Create multi-stage Dockerfile with Node.js 18 Alpine as build stage
- [x] 2.2 Add npm install and Vite build commands to Node.js stage
- [x] 2.3 Add PHP 8.2-FPM Alpine as runtime stage
- [x] 2.4 Install PHP build dependencies (gcc, g++, make, autoconf, libpng-dev, libjpeg-turbo-dev, libzip-dev)
- [x] 2.5 Install required PHP extensions (pdo, pdo_mysql, mbstring, gd, zip, redis, pcntl, fileinfo)
- [x] 2.6 Install and configure Composer in PHP stage
- [x] 2.7 Copy application files and set ownership to www-data (UID 1000)
- [x] 2.8 Copy compiled Vite assets from Node.js stage to public directory
- [x] 2.9 Run `composer install --no-dev --optimize-autoloader` in Dockerfile
- [x] 2.10 Set correct permissions on storage and bootstrap/cache directories (775)
- [x] 2.11 Configure PHP-FPM to run as www-data user
- [x] 2.12 Clean up build dependencies to reduce image size <- (verify: final image size under 150MB, all PHP extensions loaded)

## 3. Nginx Configuration

- [x] 3.1 Create `docker/nginx/default.conf` with Laravel-optimized settings
- [x] 3.2 Configure root directory as `/var/www/html/public`
- [x] 3.3 Configure index files (index.php, index.html)
- [x] 3.4 Add PHP-FPM fastcgi_pass directive pointing to `app:9000`
- [x] 3.5 Configure Laravel routing (try_files with fallback to index.php)
- [x] 3.6 Add client_max_body_size directive for large file uploads (100M)
- [x] 3.7 Configure fastcgi_params for PHP processing <- (verify: Nginx serves static files, proxies PHP to FPM, Laravel routes work)

## 4. Docker Compose Configuration

- [x] 4.1 Create `docker-compose.yml` with version 3.8
- [x] 4.2 Define `app` service (PHP-FPM) with build context and Dockerfile
- [x] 4.3 Define `webserver` service (Nginx Alpine) with port mapping 8000:80
- [x] 4.4 Define `db` service (MySQL 8) with environment variables and port 3306
- [x] 4.5 Define `redis` service (Redis Alpine) with port 6379
- [x] 4.6 Create named volume `mysql_data` mounted to `/var/lib/mysql`
- [x] 4.7 Create named volume `uploads_data` mounted to `/var/www/html/public/uploads`
- [x] 4.8 Create volume for storage directory mounted to `/var/www/html/storage`
- [x] 4.9 Mount Nginx config file to `/etc/nginx/conf.d/default.conf`
- [x] 4.10 Configure internal Docker network for service communication
- [x] 4.11 Add depends_on directives to ensure startup order (webserver depends on app, app depends on db and redis) <- (verify: all services start, can communicate via service names)

## 5. Entrypoint Script

- [x] 5.1 Create `docker-entrypoint.sh` with bash shebang and set -e
- [x] 5.2 Add database connection wait logic with retry (30 attempts, 1-second intervals)
- [x] 5.3 Add APP_KEY validation and generation if missing
- [x] 5.4 Create required storage subdirectories (framework/cache, framework/sessions, framework/views, logs)
- [x] 5.5 Set storage directory permissions (775) and ownership (www-data)
- [x] 5.6 Add `composer dump-autoload --optimize` command
- [x] 5.7 Add `php artisan migrate --force` command
- [x] 5.8 Add `php artisan view:clear` command (always run per app requirement)
- [x] 5.9 Add environment detection (APP_ENV) for conditional optimization
- [x] 5.10 Add `php artisan config:cache` for production environment only
- [x] 5.11 Add `php artisan route:cache` for production environment only
- [x] 5.12 Add `php-fpm -F` command to start PHP-FPM in foreground
- [x] 5.13 Make entrypoint script executable (chmod +x)
- [x] 5.14 Add entrypoint script to Dockerfile with ENTRYPOINT directive <- (verify: script waits for DB, runs migrations, starts PHP-FPM)

## 6. Build Context Optimization

- [x] 6.1 Add node_modules to .dockerignore
- [x] 6.2 Add vendor to .dockerignore
- [x] 6.3 Add .git directory to .dockerignore
- [x] 6.4 Add tests directory to .dockerignore
- [x] 6.5 Add storage/logs to .dockerignore
- [x] 6.6 Add .env files to .dockerignore (except .env.example)
- [x] 6.7 Add html_backup.zip to .dockerignore
- [x] 6.8 Add IDE and OS-specific files (.idea, .vscode, .DS_Store, Thumbs.db) <- (verify: build context size reduced, build time improved)

## 7. Environment Configuration

- [x] 7.1 Update .env.example with DB_HOST=db
- [x] 7.2 Update .env.example with DB_PORT=3306
- [x] 7.3 Update .env.example with DB_DATABASE=laravel
- [x] 7.4 Update .env.example with DB_USERNAME=laravel
- [x] 7.5 Update .env.example with DB_PASSWORD=secret
- [x] 7.6 Update .env.example with REDIS_HOST=redis
- [x] 7.7 Update .env.example with REDIS_PORT=6379
- [x] 7.8 Update .env.example with CACHE_DRIVER=redis
- [x] 7.9 Update .env.example with SESSION_DRIVER=redis
- [x] 7.10 Update .env.example with QUEUE_CONNECTION=sync (document Redis option for future) <- (verify: .env.example has all Docker-specific configs)

## 8. Testing and Verification

- [x] 8.1 Build Docker image with `docker-compose build`
- [x] 8.2 Start all services with `docker-compose up -d`
- [x] 8.3 Verify all four containers are running (docker-compose ps)
- [x] 8.4 Check PHP container logs for successful initialization
- [x] 8.5 Verify database connection from PHP container (docker-compose exec app php artisan tinker)
- [x] 8.6 Verify Redis connection from PHP container
- [x] 8.7 Access application at http://localhost:8000 and verify homepage loads
- [x] 8.8 Test file upload functionality to verify uploads volume persistence
- [x] 8.9 Restart containers and verify data persistence (docker-compose down && docker-compose up -d)
- [x] 8.10 Verify MySQL data persists after restart
- [x] 8.11 Verify uploaded files persist after restart
- [x] 8.12 Test multi-portal access (admin, studio, user portals)
- [x] 8.13 Verify custom rrt_ helper functions work correctly <- (verify: application fully functional, all portals accessible, data persists)

## 9. Documentation

- [x] 9.1 Create README.Docker.md with setup instructions
- [x] 9.2 Document prerequisites (Docker Engine 20.10+, Docker Compose 2.0+)
- [x] 9.3 Document initial setup steps (copy .env.example, build, start)
- [x] 9.4 Document common Docker Compose commands (up, down, logs, exec)
- [x] 9.5 Document volume backup procedures for MySQL and uploads
- [x] 9.6 Document troubleshooting steps for common issues
- [x] 9.7 Document environment variable configuration
- [x] 9.8 Document how to run artisan commands (docker-compose exec app php artisan)
- [x] 9.9 Document how to access MySQL CLI (docker-compose exec db mysql)
- [x] 9.10 Document production deployment considerations

## 10. Verification Fixes

- [x] 10.1 Remove USER www-data from Dockerfile (line 93) to avoid permission conflicts
- [x] 10.2 Install su-exec in Dockerfile for proper user switching
- [x] 10.3 Update entrypoint script to use su-exec for switching to www-data before starting PHP-FPM
- [x] 10.4 Update README.Docker.md volume names to include project prefix (1n2music_mysql_data, 1n2music_uploads_data, 1n2music_storage_data)
