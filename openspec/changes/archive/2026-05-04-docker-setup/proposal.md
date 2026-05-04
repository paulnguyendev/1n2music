## Why

The Laravel 9 music platform currently lacks containerization, making deployment inconsistent across environments and complicating local development setup. Docker will provide environment parity, simplify onboarding for new developers, and enable scalable production deployments with isolated services.

## What Changes

- Add multi-stage Dockerfile with Node.js build stage for Vite assets and PHP 8.2-FPM runtime
- Create Docker Compose orchestration for 4 services: PHP-FPM, Nginx, MySQL 8, Redis
- Configure Nginx as reverse proxy with Laravel-optimized settings
- Implement persistent volumes for MySQL data and 1.4GB uploads directory
- Add docker-entrypoint.sh for automated Laravel setup (migrations, cache, permissions)
- Update .env.example with Docker-specific database and cache configurations
- Add .dockerignore to optimize build context and reduce image size
- Configure non-root user in PHP container for security
- Expose Nginx on port 8000 (host port 80 already in use)

## Capabilities

### New Capabilities

- `docker-containerization`: Complete Docker setup with multi-stage builds, service orchestration, and production-ready configurations
- `persistent-storage`: Volume management for MySQL database and user-uploaded files
- `automated-provisioning`: Entrypoint script for Laravel initialization and environment setup

### Modified Capabilities

<!-- No existing capabilities are being modified -->

## Impact

**New Files:**
- `Dockerfile` (multi-stage: Node.js + PHP-FPM)
- `docker-compose.yml` (4 services with networking and volumes)
- `docker/nginx/default.conf` (Nginx configuration)
- `docker-entrypoint.sh` (Laravel provisioning script)
- `.dockerignore` (build optimization)

**Modified Files:**
- `.env.example` (add Docker database/cache/Redis configurations)

**Infrastructure:**
- Requires Docker Engine 20.10+ and Docker Compose 2.0+
- MySQL data persisted in named volume `mysql_data`
- Uploads directory (1.4GB) persisted in named volume `uploads_data`
- Redis for cache/sessions (ready for queue workers)

**Developer Workflow:**
- New developers: `docker-compose up -d` instead of manual LAMP setup
- Consistent PHP 8.2, MySQL 8, Redis versions across all environments
- No local PHP/Composer/Node.js installation required
