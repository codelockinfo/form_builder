#!/bin/bash
# Deployment script for Hostinger
# This script handles untracked files that conflict with Git pull

echo "Starting deployment..."

# Navigate to project directory
cd "$(dirname "$0")"

# Check if core/logger.php exists and is untracked
if [ -f "core/logger.php" ] && ! git ls-files --error-unmatch core/logger.php &>/dev/null; then
    echo "Removing untracked core/logger.php to prevent merge conflict..."
    rm -f core/logger.php
fi

# Pull latest changes
echo "Pulling latest changes from repository..."
git pull origin main

# If pull fails, try to reset and pull again
if [ $? -ne 0 ]; then
    echo "Pull failed, attempting to resolve conflicts..."
    git fetch origin
    git reset --hard origin/main
fi

echo "Deployment completed!"

