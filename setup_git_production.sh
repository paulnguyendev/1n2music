#!/bin/bash

# ============================================================================
# Git Setup Script for Production Server
# ============================================================================
# This script initializes git in existing production folder and pulls from remote
#
# Usage: bash setup_git_production.sh
# ============================================================================

set -e  # Exit on error

PROD_DIR="/opt/project/test-1n2.izidotech.com/html"
REMOTE_URL="https://github.com/paulnguyendev/1n2music.git"
BRANCH="master"

echo "=========================================="
echo "Git Setup for Production"
echo "=========================================="
echo ""

# Step 1: Navigate to production directory
echo "Step 1: Navigating to production directory..."
cd "$PROD_DIR" || { echo "Error: Cannot access $PROD_DIR"; exit 1; }
echo "✓ Current directory: $(pwd)"
echo ""

# Step 2: Initialize git (if not already initialized)
echo "Step 2: Initializing git repository..."
if [ -d .git ]; then
    echo "⚠ Git already initialized, skipping..."
else
    git init
    echo "✓ Git initialized"
fi
echo ""

# Step 3: Add remote origin
echo "Step 3: Adding remote origin..."
if git remote | grep -q "^origin$"; then
    echo "⚠ Remote 'origin' already exists"
    echo "Current remote URL: $(git remote get-url origin)"
    read -p "Do you want to update it? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git remote set-url origin "$REMOTE_URL"
        echo "✓ Remote URL updated"
    fi
else
    git remote add origin "$REMOTE_URL"
    echo "✓ Remote 'origin' added: $REMOTE_URL"
fi
echo ""

# Step 4: Fetch from remote
echo "Step 4: Fetching from remote..."
git fetch origin
echo "✓ Fetch completed"
echo ""

# Step 5: Check for local changes
echo "Step 5: Checking for local changes..."
if [ -n "$(git status --porcelain)" ]; then
    echo "⚠ WARNING: You have local changes that are not committed!"
    echo ""
    git status --short
    echo ""
    echo "Options:"
    echo "  1) Stash changes and pull (recommended - preserves your changes)"
    echo "  2) Discard all local changes and pull (DANGEROUS - loses your changes)"
    echo "  3) Cancel and review manually"
    echo ""
    read -p "Choose option (1/2/3): " -n 1 -r
    echo

    case $REPLY in
        1)
            echo "Stashing local changes..."
            git stash save "Production changes before git pull - $(date +%Y%m%d_%H%M%S)"
            echo "✓ Changes stashed"
            echo "Note: To restore later, run: git stash pop"
            ;;
        2)
            echo "⚠ WARNING: This will DELETE all local changes!"
            read -p "Are you absolutely sure? Type 'yes' to confirm: " confirm
            if [ "$confirm" = "yes" ]; then
                git reset --hard HEAD
                git clean -fd
                echo "✓ Local changes discarded"
            else
                echo "Cancelled. Exiting..."
                exit 1
            fi
            ;;
        3)
            echo "Cancelled. Please review your changes manually."
            echo "Run 'git status' to see what changed."
            exit 0
            ;;
        *)
            echo "Invalid option. Exiting..."
            exit 1
            ;;
    esac
else
    echo "✓ No local changes detected"
fi
echo ""

# Step 6: Checkout master branch
echo "Step 6: Checking out $BRANCH branch..."
git checkout -B "$BRANCH" "origin/$BRANCH"
echo "✓ Checked out $BRANCH"
echo ""

# Step 7: Pull latest changes
echo "Step 7: Pulling latest changes..."
git pull origin "$BRANCH"
echo "✓ Pull completed"
echo ""

# Step 8: Show current status
echo "=========================================="
echo "Git Setup Complete!"
echo "=========================================="
echo ""
echo "Current branch: $(git branch --show-current)"
echo "Latest commit: $(git log -1 --oneline)"
echo ""
echo "Next steps:"
echo "1. Review the changes: git log --oneline -10"
echo "2. Check file permissions: ls -la"
echo "3. Clear Laravel cache: php artisan cache:clear"
echo "4. Run database migration: mysql -u [user] -p [db] < database/sql/PRODUCTION_DEPLOYMENT.sql"
echo ""
