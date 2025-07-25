#!/bin/bash

# Quick fix script for common deployment issues
set -e

echo "🔧 Quick Fix: Resolving deployment issues..."

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# 1. Start Docker Desktop
print_status "Starting Docker Desktop..."
open -a Docker

print_status "Waiting for Docker to start..."
# Wait for Docker to be ready
for i in {1..30}; do
    if docker info >/dev/null 2>&1; then
        print_status "Docker is ready! ✓"
        break
    fi
    echo -n "."
    sleep 2
done
echo

# Check if Docker started successfully
if ! docker info >/dev/null 2>&1; then
    print_error "Docker failed to start. Please start Docker Desktop manually."
    exit 1
fi

# 2. Verify Terraform syntax
print_status "Verifying Terraform configuration..."
cd terraform
if terraform validate; then
    print_status "Terraform configuration is valid ✓"
else
    print_error "Terraform configuration has errors"
    exit 1
fi
cd ..

# 3. Test local setup first
print_status "Testing local setup..."
./local-dev.sh

print_status "Quick fix completed! ✅"
print_status "You can now run: ./execute-deployment.sh"