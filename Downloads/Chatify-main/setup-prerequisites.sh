#!/bin/bash

# Prerequisites setup script for Chatify CI/CD
set -e

echo "🔧 Setting up Chatify CI/CD Prerequisites..."

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

# Check if running on macOS
if [[ "$OSTYPE" != "darwin"* ]]; then
    print_warning "This script is optimized for macOS. Some commands may need adjustment for other systems."
fi

# 1. Check and install Homebrew (macOS)
if ! command -v brew &> /dev/null; then
    print_status "Installing Homebrew..."
    /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
else
    print_status "Homebrew already installed ✓"
fi

# 2. Install Terraform
if ! command -v terraform &> /dev/null; then
    print_status "Installing Terraform..."
    brew tap hashicorp/tap
    brew install hashicorp/tap/terraform
else
    print_status "Terraform already installed ✓"
fi

# 3. Install Docker
if ! command -v docker &> /dev/null; then
    print_status "Installing Docker..."
    brew install --cask docker
    print_warning "Please start Docker Desktop manually after installation"
else
    print_status "Docker already installed ✓"
fi

# 4. Install AWS CLI
if ! command -v aws &> /dev/null; then
    print_status "Installing AWS CLI..."
    brew install awscli
else
    print_status "AWS CLI already installed ✓"
fi

# 5. Install Node.js
if ! command -v node &> /dev/null; then
    print_status "Installing Node.js..."
    brew install node
else
    print_status "Node.js already installed ✓"
fi

# 6. Generate SSH key if not exists
if [ ! -f ~/.ssh/id_rsa ]; then
    print_status "Generating SSH key..."
    ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa -N ""
    print_status "SSH key generated at ~/.ssh/id_rsa"
else
    print_status "SSH key already exists ✓"
fi

print_status "Prerequisites setup completed!"
print_warning "Next steps:"
echo "1. Configure AWS credentials: aws configure"
echo "2. Start Docker Desktop"
echo "3. Set up GitHub repository secrets"
echo "4. Run the deployment script"