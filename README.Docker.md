# Docker Setup for 1N2 Music Platform

This document provides instructions for running the Laravel 9 music distribution platform using Docker.

## Prerequisites

- **Docker Engine**: 20.10+ ([Install Docker](https://docs.docker.com/engine/install/))
- **Docker Compose**: 2.0+ (included with Docker Desktop)
- **System Requirements**:
  - 4GB RAM minimum (8GB recommended)
  - 10GB free disk space (for images and volumes)
  - Windows users: WSL2 backend required for Docker Desktop

## Quick Start

### 1. Initial Setup

```bash
# Clone or navigate to the project directory
cd /path/to/1n2music

# Copy environment file
cp .env.example .env

# Edit .env file with your settings (optional - defaults work for local development)
# The following are already configured for Docker:
# - DB_HOST=db
# - DB_DATABASE=laravel
# - DB_USERNAME=laravel
# - DB_PASSWORD=secret
# - REDIS_HOST=redis
# - CACHE_DRIVER=redis
# - SESSION_DRIVER=redis
```

### 2. Build and Start

```bash
# Build Docker images (first time only, ~5-10 minutes)
docker-compose build

# Start all services in detached mode
docker-compose up -d

# View logs to monitor initialization
docker-compose logs -f app
```

### 3. Access the Application

- **Main Application**: http://localhost:8000
- **Admin Portal**: http://localhost:8000/admin
- **Studio Portal**: http://localhost:8000/studio
- **User Portal**: http://localhost:8000/giang

The entrypoint script automatically:
- Waits for database to be ready
- Generates APP_KEY if missing
- Runs database migrations
- Sets up storage directories with correct permissions
- Clears view cache (per application requirement)

## Docker Compose Commands

### Service Management

```bash
# Start all services
docker-compose up -d

# Stop all services (keeps data)
docker-compose down

# Stop and remove volumes (WARNING: deletes all data)
docker-compose down -v

# Restart a specific service
docker-compose restart app

# View service status
docker-compose ps

# View logs
docker-compose logs -f          # All services
docker-compose logs -f app      # PHP-FPM only
docker-compose logs -f webserver # Nginx only
```

### Rebuilding

```bash
# Rebuild after code changes
docker-compose build

# Rebuild without cache (clean build)
docker-compose build --no-cache

# Rebuild and restart
docker-compose up -d --build
```

## Running Artisan Commands

Execute Laravel artisan commands inside the PHP container:

```bash
# General syntax
docker-compose exec app php artisan <command>

# Examples
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:list
docker-compose exec app php artisan tinker

# Run as www-data user (if needed)
docker-compose exec -u www-data app php artisan <command>
```

## Database Access

### MySQL CLI

```bash
# Access MySQL CLI
docker-compose exec db mysql -u laravel -p
# Password: secret (or your DB_PASSWORD from .env)

# Access as root
docker-compose exec db mysql -u root -p
# Password: root (or your DB_ROOT_PASSWORD from .env)

# Run SQL file
docker-compose exec -T db mysql -u laravel -p laravel < backup.sql
```

### Database Migrations

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Rollback last migration
docker-compose exec app php artisan migrate:rollback

# Reset and re-run all migrations
docker-compose exec app php artisan migrate:fresh

# Run migrations with seeding
docker-compose exec app php artisan migrate --seed
```

## Volume Management and Backups

### Understanding Volumes

The setup uses three named volumes for data persistence:
- `1n2music_mysql_data`: MySQL database files
- `1n2music_uploads_data`: User-uploaded files (public/uploads)
- `1n2music_storage_data`: Laravel storage directory (logs, cache, sessions)

### Backup MySQL Database

```bash
# Create SQL dump
docker-compose exec db mysqldump -u laravel -p laravel > backup_$(date +%Y%m%d).sql

# Restore from SQL dump
docker-compose exec -T db mysql -u laravel -p laravel < backup_20260504.sql
```

### Backup Uploaded Files

```bash
# Backup uploads volume to tar.gz
docker run --rm \
  -v 1n2music_uploads_data:/data \
  -v $(pwd):/backup \
  alpine tar czf /backup/uploads_backup_$(date +%Y%m%d).tar.gz -C /data .

# Restore uploads from tar.gz
docker run --rm \
  -v 1n2music_uploads_data:/data \
  -v $(pwd):/backup \
  alpine tar xzf /backup/uploads_backup_20260504.tar.gz -C /data
```

### Backup Storage Directory

```bash
# Backup storage volume
docker run --rm \
  -v 1n2music_storage_data:/data \
  -v $(pwd):/backup \
  alpine tar czf /backup/storage_backup_$(date +%Y%m%d).tar.gz -C /data .

# Restore storage
docker run --rm \
  -v 1n2music_storage_data:/data \
  -v $(pwd):/backup \
  alpine tar xzf /backup/storage_backup_20260504.tar.gz -C /data
```

### List and Inspect Volumes

```bash
# List all volumes
docker volume ls

# Inspect volume details
docker volume inspect 1n2music_mysql_data
docker volume inspect 1n2music_uploads_data
docker volume inspect 1n2music_storage_data

# Remove unused volumes (WARNING: data loss)
docker volume prune
```

## Environment Variables

### Required Variables

These are pre-configured in `.env.example` for Docker:

```env
# Database (Docker service names)
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

# Redis (Docker service name)
REDIS_HOST=redis
REDIS_PORT=6379

# Cache and Sessions (using Redis)
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Queue (sync for now, can change to redis later)
QUEUE_CONNECTION=sync
```

### Optional Customization

```env
# Application
APP_NAME="1N2 Music"
APP_ENV=local          # Use 'production' for prod optimizations
APP_DEBUG=true         # Set to false in production
APP_URL=http://localhost:8000

# Database Root Password (for MySQL root user)
DB_ROOT_PASSWORD=root

# Mail Configuration (if using mail features)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

### Production Environment

For production deployments, set `APP_ENV=production` in `.env`. This enables:
- Configuration caching (`php artisan config:cache`)
- Route caching (`php artisan route:cache`)
- Optimized autoloader
- Disabled debug mode (set `APP_DEBUG=false`)

## Troubleshooting

### Container Won't Start

```bash
# Check container logs
docker-compose logs app

# Check all service logs
docker-compose logs

# Verify all services are running
docker-compose ps
```

### Database Connection Errors

```bash
# Verify database is running
docker-compose ps db

# Check database logs
docker-compose logs db

# Test connection from app container
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo();

# Verify environment variables
docker-compose exec app env | grep DB_
```

### Permission Errors

```bash
# Fix storage permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache

# Recreate storage directories
docker-compose exec app mkdir -p storage/framework/{cache,sessions,views} storage/logs
```

### Port Already in Use

If port 8000 is already in use, edit `docker-compose.yml`:

```yaml
webserver:
  ports:
    - "8080:80"  # Change 8000 to 8080 or any available port
```

### Rebuild After Code Changes

```bash
# Frontend assets changed
docker-compose build --no-cache
docker-compose up -d

# PHP code changed (no rebuild needed)
docker-compose restart app

# Composer dependencies changed
docker-compose build
docker-compose up -d
```

### Clear All Caches

```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### View Cache Clearing

The application automatically runs `php artisan view:clear` on every request (see `routes/web.php`). This is intentional per the application's requirements. In Docker, this is also run during container initialization.

### MySQL Connection Timeout

If the entrypoint script times out waiting for MySQL:

```bash
# Increase wait time in docker-entrypoint.sh (line 8)
RETRIES=60  # Change from 30 to 60

# Or start database first, then app
docker-compose up -d db
sleep 10
docker-compose up -d app webserver redis
```

### Large Uploads Volume

The uploads directory may contain 1.4GB of files. Initial volume population:

```bash
# Copy existing uploads to volume
docker-compose up -d
docker cp ./public/uploads/. 1n2music-app:/var/www/html/public/uploads/
docker-compose exec app chown -R www-data:www-data /var/www/html/public/uploads
```

## Production Deployment Considerations

### Security

1. **Change default passwords** in `.env`:
   ```env
   DB_PASSWORD=<strong-random-password>
   DB_ROOT_PASSWORD=<strong-random-password>
   ```

2. **Set production environment**:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

3. **Use Docker secrets** for sensitive data (Docker Swarm/Kubernetes)

4. **Enable HTTPS** via reverse proxy (Nginx, Traefik, or load balancer)

### Performance

1. **Use external database** (AWS RDS, managed MySQL) instead of containerized MySQL
2. **Use object storage** (S3, DigitalOcean Spaces) for uploads instead of volume
3. **Use external Redis** (AWS ElastiCache, managed Redis) for better performance
4. **Enable OPcache** in PHP configuration
5. **Use CDN** for static assets

### Scaling

1. **Horizontal scaling**: Run multiple app containers behind load balancer
2. **Queue workers**: Add dedicated queue worker containers
   ```yaml
   queue:
     build: .
     command: php artisan queue:work redis --sleep=3 --tries=3
     depends_on:
       - app
       - redis
   ```
3. **Cron scheduler**: Add dedicated scheduler container
   ```yaml
   scheduler:
     build: .
     command: php artisan schedule:work
     depends_on:
       - app
   ```

### Monitoring

1. **Health checks**: Add Docker health checks to services
2. **Logging**: Configure centralized logging (ELK, CloudWatch)
3. **Metrics**: Use Prometheus + Grafana for monitoring
4. **APM**: Consider New Relic, Datadog, or Laravel Telescope

### Backup Strategy

1. **Automated backups**: Schedule daily database and volume backups
2. **Off-site storage**: Store backups in S3 or equivalent
3. **Backup retention**: Keep 7 daily, 4 weekly, 12 monthly backups
4. **Test restores**: Regularly test backup restoration process

## Architecture Overview

### Services

- **app**: PHP 8.2-FPM container running Laravel application
- **webserver**: Nginx Alpine serving static files and proxying PHP requests
- **db**: MySQL 8 database with persistent storage
- **redis**: Redis Alpine for cache and sessions

### Networking

All services communicate via internal `app-network` bridge network using service names as hostnames.

### Volumes

- `mysql_data`: Persists MySQL database files
- `uploads_data`: Persists user-uploaded files (1.4GB+)
- `storage_data`: Persists Laravel logs, cache, and sessions

### Build Process

1. **Node.js stage**: Installs npm dependencies and compiles Vite assets
2. **PHP stage**: Installs PHP extensions, Composer dependencies, copies compiled assets
3. **Optimization**: Removes build dependencies to reduce final image size

### Initialization Flow

1. Container starts, entrypoint script executes
2. Waits for MySQL to be ready (30 retries, 1-second intervals)
3. Validates/generates APP_KEY
4. Creates storage directories with correct permissions
5. Runs Composer autoload optimization
6. Runs database migrations
7. Clears view cache
8. Runs production optimizations (if APP_ENV=production)
9. Starts PHP-FPM in foreground

## Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Laravel Deployment Documentation](https://laravel.com/docs/9.x/deployment)
- [Laravel Docker Best Practices](https://laravel.com/docs/9.x/sail)

## Support

For issues specific to this Docker setup, check:
1. Container logs: `docker-compose logs`
2. Service status: `docker-compose ps`
3. Environment variables: `docker-compose exec app env`
4. PHP extensions: `docker-compose exec app php -m`
5. Database connectivity: `docker-compose exec app php artisan tinker`

For application-specific issues, refer to the main project documentation.
