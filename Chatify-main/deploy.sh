#!/bin/bash

# Main Chatify Deployment Script
# This script orchestrates the deployment from the project root

set -e

echo "🚀 Chatify CI/CD Deployment"
echo "=========================="

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
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

print_header() {
    echo -e "${BLUE}$1${NC}"
}

# Check if we're in the right directory
if [ ! -d "Chatify" ] || [ ! -d "infrastructure" ]; then
    print_error "Please run this script from the project root directory"
    print_error "Expected structure:"
    echo "  ├── Chatify/           # Application code"
    echo "  ├── infrastructure/    # DevOps configuration"
    echo "  └── deploy.sh         # This script"
    exit 1
fi

# Show project structure
print_header "📁 Project Structure:"
echo "├── Chatify/                    # Application"
echo "│   ├── frontend/              # React app"
echo "│   ├── server/               # Node.js backend"
echo "│   ├── Dockerfile            # App container"
echo "│   ├── docker-compose.yml    # Services"
echo "│   └── Jenkinsfile          # CI/CD pipeline"
echo "├── infrastructure/           # DevOps"
echo "│   ├── terraform/           # AWS infrastructure"
echo "│   ├── nagios/             # Monitoring"
echo "│   ├── scripts/            # Deployment scripts"
echo "│   └── github-actions/     # CI/CD workflows"
echo "└── deploy.sh               # Main deployment script"
echo

# Run the main deployment script
print_status "Running deployment from infrastructure/scripts/..."
cd infrastructure/scripts
./execute-deployment.sh "$@"