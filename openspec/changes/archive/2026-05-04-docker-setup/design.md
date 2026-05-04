## Context

The Laravel 9 music platform (1N2 Music) is a complex multi-portal application with custom naming conventions (rrt_ prefixes), three separate portals (Admin, Studio, User), and extensive integrations (PayPal, YouTube API, reCAPTCHA). The application currently runs on traditional LAMP stack with manual setup, leading to environment inconsistencies and difficult onboarding.

**Current State:**
- Laravel 9 with PHP ^8.0.2 requirement
- MySQL database with rrt_ table prefixes
- 1.4GB uploads directory in `public/uploads`
- Vite for frontend asset compilation
- Custom helper functions auto-loaded via Composer
- View cache cleared on every request (routes/web.php line 8)
- No containerization or orchestration

**Constraints:**
- Must support existing codebase without modifications
- Port 80 already in use on host machine (use 8000)
- Large uploads directory requires persistent storage
- Multi-stage build needed for Vite compilation
- Must maintain compatibility with custom rrt_ helper functions

**Stakeholders:**
- Developers: Need consistent local development environment
- DevOps: Need reproducible deployments
- New team members: Need simplified onboarding

## Goals / Non-Goals

**Goals:**
- Provide complete Docker setup with single-command startup (`docker-compose up -d`)
- Ensure environment parity between development and production
- Persist critical data (MySQL database, uploaded files)
- Optimize image size with multi-stage builds
- Automate Laravel initialization (migrations, cache, permissions)
- Support existing Laravel 9 codebase without code changes
- Enable scalable production deployments

**Non-Goals:**
- Kubernetes orchestration (Docker Compose only)
- CI/CD pipeline configuration (separate concern)
- Database seeding or fixture management
- Queue worker containers (queue currently uses 'sync' driver)
- Horizontal scaling configuration (single-instance focus)
- SSL/TLS certificate management (handled by reverse proxy/load balancer)

## Decisions

### Decision 1: Multi-stage Dockerfile (Node.js + PHP)

**Choice:** Use multi-stage build with Node.js 18 Alpine for Vite compilation and PHP 8.2-FPM Alpine for runtime.

**Rationale:**
- Vite requires Node.js for asset compilation
- Final image should not include Node.js (reduces size by ~200MB)
- Alpine base images minimize attack surface and image size
- PHP 8.2 provides performance improvements over 8.0 while maintaining compatibility

**Alternatives Considered:**
- Single-stage with Node.js + PHP: Rejected due to bloated final image
- Pre-compiled assets in repo: Rejected due to merge conflicts and repo size
- Separate build pipeline: Rejected due to added complexity for simple setup

### Decision 2: PHP 8.2-FPM Alpine as Base Image

**Choice:** Use `php:8.2-fpm-alpine` as the PHP runtime base.

**Rationale:**
- Laravel 9 requires PHP ^8.0.2, and 8.2 is stable and performant
- FPM (FastCGI Process Manager) is production-standard for Nginx + PHP
- Alpine reduces image size from ~400MB (Debian) to ~80MB
- All required extensions available via apk and pecl

**Alternatives Considered:**
- php:8.0-fpm: Rejected due to missing performance improvements in 8.2
- php:8.2-fpm (Debian): Rejected due to larger image size
- php:8.2-apache: Rejected because Nginx is more performant for Laravel

### Decision 3: Named Volumes for Persistence

**Choice:** Use Docker named volumes for MySQL data and uploads directory.

**Rationale:**
- Named volumes persist across container removals
- Docker manages volume lifecycle and permissions
- Easier backup/restore with `docker run --rm -v` commands
- Avoids host filesystem permission issues on Windows/Mac

**Alternatives Considered:**
- Bind mounts: Rejected due to permission issues across OS platforms
- Volume containers: Rejected as deprecated pattern
- Host directories: Rejected due to Windows path compatibility issues

### Decision 4: Nginx as Reverse Proxy

**Choice:** Use Nginx Alpine as web server with PHP-FPM backend.

**Rationale:**
- Industry standard for Laravel production deployments
- Better performance than Apache for static files
- Smaller memory footprint
- Easier configuration for Laravel routing

**Alternatives Considered:**
- Apache with mod_php: Rejected due to higher memory usage
- Caddy: Rejected due to team familiarity with Nginx
- Traefik: Rejected as overkill for single-app setup

### Decision 5: Port 8000 for Host Binding

**Choice:** Map Nginx port 80 to host port 8000 (8000:80).

**Rationale:**
- Port 80 already in use on host machine
- Port 8000 is common alternative for development
- Avoids conflicts with existing services

**Alternatives Considered:**
- Port 8080: Rejected due to common use by other dev tools
- Port 3000: Rejected due to common use by frontend dev servers

### Decision 6: Entrypoint Script for Initialization

**Choice:** Use bash entrypoint script with database wait logic, migrations, and optimization commands.

**Rationale:**
- Ensures database is ready before running migrations
- Automates repetitive setup tasks
- Handles environment-specific optimizations (dev vs prod)
- Maintains Laravel best practices (config:cache, route:cache)

**Alternatives Considered:**
- Docker healthchecks only: Rejected as insufficient for Laravel setup
- Separate init container: Rejected as over-engineered for this use case
- Manual setup: Rejected as defeats automation goal

### Decision 7: Non-root User (www-data)

**Choice:** Run PHP-FPM as www-data user (UID 1000) instead of root.

**Rationale:**
- Security best practice (principle of least privilege)
- Matches standard Nginx/PHP-FPM configuration
- Prevents accidental permission escalation
- UID 1000 matches common developer user IDs

**Alternatives Considered:**
- Root user: Rejected due to security risks
- Custom user: Rejected due to added complexity
- UID 82 (Alpine www-data): Rejected due to permission issues with volumes

### Decision 8: Redis for Cache and Sessions

**Choice:** Include Redis Alpine container for cache and session storage.

**Rationale:**
- Faster than file-based cache/sessions
- Prepares for future queue worker implementation
- Minimal resource overhead with Alpine image
- Industry standard for Laravel caching

**Alternatives Considered:**
- File-based cache: Rejected due to performance limitations
- Memcached: Rejected due to Redis being more feature-rich
- No cache: Rejected as Laravel benefits significantly from caching

## Risks / Trade-offs

### Risk 1: Large Uploads Volume (1.4GB)
**Risk:** Initial volume population may take time; volume backups are large.
**Mitigation:** Document backup procedures; consider object storage (S3) for future optimization.

### Risk 2: Database Migration Failures
**Risk:** Entrypoint script runs migrations automatically, which could fail and prevent container startup.
**Mitigation:** Implement retry logic with exponential backoff; log migration errors clearly; provide manual migration option via `docker-compose exec`.

### Risk 3: View Cache Clearing on Every Request
**Risk:** Application clears view cache on every request (routes/web.php line 8), which may impact performance.
**Mitigation:** Document this behavior; consider removing in production; ensure storage directory has fast I/O.

### Risk 4: Windows Path Compatibility
**Risk:** Docker on Windows may have path issues with volumes and bind mounts.
**Mitigation:** Use named volumes instead of bind mounts; test on Windows Docker Desktop; document WSL2 requirement.

### Risk 5: Image Build Time
**Risk:** Multi-stage build with npm install and composer install may take 5-10 minutes on first build.
**Mitigation:** Use Docker layer caching; document expected build time; consider CI/CD caching strategies.

### Risk 6: PHP Extension Compilation
**Risk:** Some PHP extensions (gd, zip) require compilation on Alpine, increasing build time.
**Mitigation:** Install build dependencies in single RUN layer; clean up after installation to reduce image size.

### Risk 7: Environment Variable Management
**Risk:** Sensitive credentials in .env file may be accidentally committed or exposed.
**Mitigation:** Document .env.example usage; add .env to .gitignore; recommend Docker secrets for production.

## Migration Plan

**Phase 1: Local Development Setup**
1. Create Dockerfile, docker-compose.yml, and supporting files
2. Test build process: `docker-compose build`
3. Test startup: `docker-compose up -d`
4. Verify application access at http://localhost:8000
5. Test database connectivity and migrations
6. Test file uploads to verify volume persistence

**Phase 2: Developer Onboarding**
1. Update README with Docker setup instructions
2. Document environment variable configuration
3. Provide troubleshooting guide for common issues
4. Train team on Docker Compose commands

**Phase 3: Production Deployment (Future)**
1. Create production docker-compose.override.yml
2. Configure external database (RDS/managed MySQL)
3. Configure object storage for uploads (S3/equivalent)
4. Set up container orchestration (ECS/Kubernetes)
5. Implement health checks and monitoring

**Rollback Strategy:**
- Keep existing LAMP setup documentation
- Docker setup is additive (no code changes required)
- Developers can continue using local PHP/MySQL if Docker issues arise
- Database migrations are reversible via Laravel migration rollback

## Open Questions

1. **Queue Workers:** Should we add a separate queue worker container now or wait until queue driver changes from 'sync'?
   - **Recommendation:** Wait until queue driver is changed to Redis/database.

2. **Cron Jobs:** Does the application have scheduled tasks that need a cron container?
   - **Recommendation:** Investigate Laravel scheduler usage; add cron container if needed.

3. **Development vs Production Compose Files:** Should we split docker-compose.yml into base + dev/prod overrides?
   - **Recommendation:** Start with single file; split when production requirements diverge.

4. **SSL Certificates:** How should SSL be handled in production?
   - **Recommendation:** Use external reverse proxy (Traefik/Nginx) or load balancer for SSL termination.

5. **Database Initialization:** Should we include database seeding in entrypoint script?
   - **Recommendation:** No, keep seeding as manual step to avoid accidental data overwrites.
