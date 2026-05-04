# Git Setup Guide for Production Server

## Current Situation
- **Production folder**: `/opt/project/test-1n2.izidotech.com/html/`
- **Status**: Folder exists with code, but no git initialized
- **Remote repository**: `https://github.com/paulnguyendev/1n2music.git`
- **Branch**: `master`

---

## Option 1: Automated Setup (Recommended)

### Step 1: Upload script to server
```bash
# From your local machine, upload the script
scp setup_git_production.sh user@server:/tmp/

# Or copy content manually and create file on server
```

### Step 2: Run the script
```bash
# SSH to production server
ssh user@server

# Make script executable
chmod +x /tmp/setup_git_production.sh

# Run the script
sudo bash /tmp/setup_git_production.sh
```

The script will:
- Initialize git in production folder
- Add remote origin
- Handle local changes safely (stash or discard)
- Pull latest code from GitHub
- Show summary

---

## Option 2: Manual Setup

### Step 1: Backup current code
```bash
# SSH to production server
ssh user@server

# Create backup
sudo cp -r /opt/project/test-1n2.izidotech.com/html /opt/project/test-1n2.izidotech.com/html.backup.$(date +%Y%m%d_%H%M%S)

# Verify backup
ls -la /opt/project/test-1n2.izidotech.com/
```

### Step 2: Navigate to production folder
```bash
cd /opt/project/test-1n2.izidotech.com/html
pwd  # Verify you're in the right directory
```

### Step 3: Initialize git
```bash
# Initialize git repository
git init

# Verify git is initialized
ls -la .git
```

### Step 4: Add remote repository
```bash
# Add remote origin
git remote add origin https://github.com/paulnguyendev/1n2music.git

# Verify remote
git remote -v
# Should show:
# origin  https://github.com/paulnguyendev/1n2music.git (fetch)
# origin  https://github.com/paulnguyendev/1n2music.git (push)
```

### Step 5: Fetch from remote
```bash
# Fetch all branches and commits
git fetch origin

# List available branches
git branch -r
```

### Step 6: Handle local changes

**Option A: Stash local changes (Recommended - preserves your changes)**
```bash
# Check what files have changed
git status

# Stash all local changes
git stash save "Production changes before git pull - $(date +%Y%m%d_%H%M%S)"

# Verify stash
git stash list
```

**Option B: Discard local changes (DANGEROUS - loses your changes)**
```bash
# ⚠️ WARNING: This will DELETE all local changes!
# Only use if you're sure you don't need local modifications

git reset --hard HEAD
git clean -fd
```

### Step 7: Checkout master branch
```bash
# Checkout master branch from remote
git checkout -b master origin/master

# Or if master already exists locally
git checkout master
git reset --hard origin/master
```

### Step 8: Pull latest changes
```bash
# Pull latest code
git pull origin master

# Verify current commit
git log -1 --oneline
# Should show: 7aca28a Add production deployment script and checklist
```

### Step 9: Verify files
```bash
# Check that new files exist
ls -la database/sql/PRODUCTION_DEPLOYMENT.sql
ls -la PRODUCTION_DEPLOYMENT_CHECKLIST.md
ls -la test_user_role.php
ls -la test_release_limits.php

# Check modified files
git log --oneline -5
```

---

## Option 3: Fresh Clone (If too many conflicts)

If there are too many local changes and conflicts:

```bash
# Backup current folder
sudo mv /opt/project/test-1n2.izidotech.com/html /opt/project/test-1n2.izidotech.com/html.old

# Clone fresh from GitHub
cd /opt/project/test-1n2.izidotech.com/
sudo git clone https://github.com/paulnguyendev/1n2music.git html

# Copy important files from old folder (if needed)
# Example: .env file
sudo cp html.old/.env html/.env

# Set proper permissions
sudo chown -R www-data:www-data html/
sudo chmod -R 755 html/
```

---

## Post-Setup Steps

### 1. Verify Git Setup
```bash
cd /opt/project/test-1n2.izidotech.com/html

# Check current branch
git branch --show-current
# Should show: master

# Check remote
git remote -v

# Check latest commits
git log --oneline -5
```

### 2. Set File Permissions
```bash
# Set owner (adjust user/group as needed)
sudo chown -R www-data:www-data /opt/project/test-1n2.izidotech.com/html

# Set directory permissions
sudo find /opt/project/test-1n2.izidotech.com/html -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /opt/project/test-1n2.izidotech.com/html -type f -exec chmod 644 {} \;

# Make storage and cache writable
sudo chmod -R 775 /opt/project/test-1n2.izidotech.com/html/storage
sudo chmod -R 775 /opt/project/test-1n2.izidotech.com/html/bootstrap/cache
```

### 3. Clear Laravel Cache
```bash
cd /opt/project/test-1n2.izidotech.com/html

php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 4. Run Database Migration
```bash
# Connect to database
mysql -u [username] -p [database_name]

# Run the deployment script
source /opt/project/test-1n2.izidotech.com/html/database/sql/PRODUCTION_DEPLOYMENT.sql

# Review verification results
# If OK: COMMIT;
# If issues: ROLLBACK;
```

### 5. Test the Application
```bash
# Run test scripts
cd /opt/project/test-1n2.izidotech.com/html

php test_user_role.php
# Expected: 21/21 tests passed

php test_release_limits.php
# Expected: 8/9 tests passed
```

---

## Troubleshooting

### Issue: Permission Denied
```bash
# Run commands with sudo
sudo git init
sudo git remote add origin https://github.com/paulnguyendev/1n2music.git
```

### Issue: Git already initialized
```bash
# Check existing remote
git remote -v

# Update remote URL if different
git remote set-url origin https://github.com/paulnguyendev/1n2music.git
```

### Issue: Merge conflicts
```bash
# See conflicting files
git status

# Option 1: Keep remote version (discard local)
git checkout --theirs [file]

# Option 2: Keep local version
git checkout --ours [file]

# Option 3: Manually resolve
# Edit the file, then:
git add [file]
git commit -m "Resolved merge conflict"
```

### Issue: Detached HEAD state
```bash
# Create and checkout master branch
git checkout -b master

# Set upstream
git branch --set-upstream-to=origin/master master
```

---

## Future Updates

After initial setup, updating is simple:

```bash
cd /opt/project/test-1n2.izidotech.com/html

# Pull latest changes
git pull origin master

# Clear cache
php artisan cache:clear

# Restart services if needed
sudo systemctl restart php-fpm  # or php7.4-fpm, php8.1-fpm, etc.
sudo systemctl restart nginx    # or apache2
```

---

## Rollback (If Needed)

If something goes wrong:

```bash
# Go back to previous commit
git log --oneline -10  # Find the commit hash
git reset --hard [commit-hash]

# Or restore from backup
sudo rm -rf /opt/project/test-1n2.izidotech.com/html
sudo mv /opt/project/test-1n2.izidotech.com/html.backup.YYYYMMDD_HHMMSS /opt/project/test-1n2.izidotech.com/html
```

---

## Notes

- Always backup before making changes
- Test on staging environment first if possible
- Keep .env file safe (it's in .gitignore)
- Monitor error logs after deployment
- Have rollback plan ready

---

## Support

If you encounter issues:
1. Check error logs: `tail -f /var/log/nginx/error.log`
2. Check Laravel logs: `tail -f storage/logs/laravel.log`
3. Verify file permissions
4. Check database connection
