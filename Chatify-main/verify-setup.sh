#!/bin/bash

# Verification script to check if everything is ready for GitHub commit
set -e

echo "🔍 Verifying Chatify CI/CD Setup for GitHub"
echo "==========================================="

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_check() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}!${NC} $1"
}

print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

errors=0

echo "📁 Checking project structure..."

# Check main directories
if [ -d "Chatify" ]; then
    print_check "Chatify/ directory exists"
else
    print_error "Chatify/ directory missing"
    ((errors++))
fi

if [ -d "infrastructure" ]; then
    print_check "infrastructure/ directory exists"
else
    print_error "infrastructure/ directory missing"
    ((errors++))
fi

if [ -d ".github/workflows" ]; then
    print_check ".github/workflows/ directory exists"
else
    print_error ".github/workflows/ directory missing"
    ((errors++))
fi

# Check key files
key_files=(
    "Chatify/Dockerfile"
    "Chatify/docker-compose.yml"
    "Chatify/Jenkinsfile"
    "infrastructure/terraform/main.tf"
    "infrastructure/terraform/variables.tf"
    "infrastructure/terraform/outputs.tf"
    "infrastructure/nagios/config/chatify.cfg"
    "infrastructure/scripts/execute-deployment.sh"
    "infrastructure/scripts/health-check.sh"
    ".github/workflows/ci.yml"
    ".github/workflows/cd.yml"
    "deploy.sh"
    ".gitignore"
    "SETUP-GITHUB.md"
)

echo
echo "📄 Checking key files..."
for file in "${key_files[@]}"; do
    if [ -f "$file" ]; then
        print_check "$file"
    else
        print_error "$file missing"
        ((errors++))
    fi
done

# Check if scripts are executable
echo
echo "🔧 Checking script permissions..."
scripts=(
    "deploy.sh"
    "infrastructure/scripts/execute-deployment.sh"
    "infrastructure/scripts/health-check.sh"
    "infrastructure/scripts/setup-prerequisites.sh"
    "infrastructure/scripts/local-dev.sh"
)

for script in "${scripts[@]}"; do
    if [ -f "$script" ] && [ -x "$script" ]; then
        print_check "$script is executable"
    elif [ -f "$script" ]; then
        print_warning "$script exists but not executable (will fix)"
        chmod +x "$script"
        print_check "Fixed permissions for $script"
    else
        print_error "$script missing"
        ((errors++))
    fi
done

# Check for sensitive files that shouldn't be committed
echo
echo "🔒 Checking for sensitive files..."
sensitive_patterns=(
    "*.tfstate"
    "*.tfvars"
    ".terraform/"
    "*.pem"
    "*.key"
    "id_rsa*"
    "aws-credentials*"
    "instance_ip.txt"
)

found_sensitive=false
for pattern in "${sensitive_patterns[@]}"; do
    if ls $pattern 2>/dev/null | grep -q .; then
        print_warning "Found sensitive files matching: $pattern"
        found_sensitive=true
    fi
done

if [ "$found_sensitive" = false ]; then
    print_check "No sensitive files found"
fi

# Check Node.js dependencies
echo
echo "📦 Checking Node.js setup..."
if [ -f "Chatify/frontend/package.json" ] && [ -f "Chatify/server/package.json" ]; then
    print_check "package.json files exist"
    
    # Check if node_modules are gitignored
    if grep -q "node_modules/" .gitignore 2>/dev/null; then
        print_check "node_modules/ is in .gitignore"
    else
        print_warning "node_modules/ should be in .gitignore"
    fi
else
    print_error "Missing package.json files"
    ((errors++))
fi

# Summary
echo
echo "📊 Verification Summary"
echo "======================"

if [ $errors -eq 0 ]; then
    print_check "All checks passed! Ready for GitHub commit."
    echo
    print_info "Next steps:"
    echo "1. git add ."
    echo "2. git commit -m 'Add complete CI/CD pipeline'"
    echo "3. git push origin main"
    echo "4. Configure GitHub secrets (see SETUP-GITHUB.md)"
    echo
    print_info "After pushing, check SETUP-GITHUB.md for configuration steps."
else
    print_error "Found $errors error(s). Please fix before committing."
    exit 1
fi

echo
print_info "Repository structure:"
echo "├── Chatify/                    # Your application"
echo "├── infrastructure/             # DevOps configuration"  
echo "├── .github/workflows/          # GitHub Actions"
echo "├── deploy.sh                   # Main deployment script"
echo "├── SETUP-GITHUB.md             # Setup instructions"
echo "└── .gitignore                  # Git ignore rules"