#!/bin/bash

# Local development setup for Chatify
set -e

echo "🏠 Setting up Chatify for local development..."

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

# 1. Install dependencies
print_status "Installing dependencies..."

# Frontend dependencies
cd Chatify/frontend
npm install
cd ../..

# Backend dependencies  
cd Chatify/server
npm install
cd ../..

# 2. Run tests locally
print_status "Running tests..."

# Frontend tests
cd Chatify/frontend
npm test -- --coverage --watchAll=false || true
cd ../..

# Backend linting
cd Chatify/server
npm run lint || true
cd ../..

# 3. Build Docker image locally
print_status "Building Docker image..."
docker build -t chatify:local ./Chatify

# 4. Test Docker image
print_status "Testing Docker image..."
docker run -d --name chatify-test -p 3002:3000 chatify:local
sleep 10

if curl -f http://localhost:3002; then
    print_status "Local Docker test passed ✓"
else
    print_error "Local Docker test failed ✗"
fi

# Cleanup
docker stop chatify-test
docker rm chatify-test

print_status "Local development setup completed!"
print_status "You can now:"
echo "  • Run frontend: cd Chatify/frontend && npm start"
echo "  • Run backend: cd Chatify/server && npm run dev"
echo "  • Test Docker: docker run -p 3000:3000 chatify:local"