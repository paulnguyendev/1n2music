# Verification Fixes Log

## [2026-05-04] Round 1 (from apply auto-verify)

### WARNING 1 - User Permission Conflict

**Fixed:**
- Removed `USER www-data` directive from Dockerfile line 93 to prevent permission conflicts during entrypoint execution
- Added `su-exec` package installation in Dockerfile for proper user switching capability
- Updated `docker-entrypoint.sh` to use `su-exec www-data php-fpm -F` instead of direct `php-fpm -F` execution
- Entrypoint now runs as root (allowing chmod/chown operations), then switches to www-data user before starting PHP-FPM

**Files Modified:**
- `D:\Workspaces\projects\1n2music\Dockerfile` (lines 88-96)
- `D:\Workspaces\projects\1n2music\docker-entrypoint.sh` (lines 68-72)

### WARNING 2 - Volume Backup Documentation

**Fixed:**
- Updated README.Docker.md section "Understanding Volumes" to use correct prefixed volume names
- Changed volume names from `mysql_data`, `uploads_data`, `storage_data` to `1n2music_mysql_data`, `1n2music_uploads_data`, `1n2music_storage_data`
- All three volumes (mysql_data, uploads_data, storage_data) are now properly documented with correct naming convention
- Backup procedures already included all three volumes with correct commands

**Files Modified:**
- `D:\Workspaces\projects\1n2music\README.Docker.md` (lines 159-162)

### Tasks Updated

Added section 10 "Verification Fixes" to tasks.md with 4 completed tasks documenting the fixes applied.

**Files Modified:**
- `D:\Workspaces\projects\1n2music\openspec\changes\docker-setup\tasks.md` (lines 115-120)
