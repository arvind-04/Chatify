#!/bin/bash

# Chatify Deployment Script
set -e

echo "🚀 Starting Chatify deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if required tools are installed
check_requirements() {
    print_status "Checking requirements..."
    
    if ! command -v terraform &> /dev/null; then
        print_error "Terraform is not installed. Please install Terraform first."
        exit 1
    fi
    
    if ! command -v docker &> /dev/null; then
        print_error "Docker is not installed. Please install Docker first."
        exit 1
    fi
    
    if ! command -v aws &> /dev/null; then
        print_warning "AWS CLI is not installed. Some features may not work."
    fi
    
    print_status "Requirements check completed."
}

# Deploy infrastructure with Terraform
deploy_infrastructure() {
    print_status "Deploying infrastructure with Terraform..."
    
    cd terraform
    
    # Initialize Terraform
    terraform init
    
    # Plan deployment
    terraform plan
    
    # Apply deployment
    read -p "Do you want to apply the Terraform plan? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        terraform apply -auto-approve
        
        # Get outputs
        INSTANCE_IP=$(terraform output -raw instance_public_ip)
        print_status "Instance deployed at IP: $INSTANCE_IP"
        
        # Save IP to file for later use
        echo "$INSTANCE_IP" > ../instance_ip.txt
    else
        print_warning "Terraform apply cancelled."
        exit 1
    fi
    
    cd ..
}

# Deploy application using Docker Compose
deploy_application() {
    print_status "Deploying application..."
    
    if [ -f "instance_ip.txt" ]; then
        INSTANCE_IP=$(cat instance_ip.txt)
        print_status "Deploying to instance: $INSTANCE_IP"
        
        # Copy files to instance (assuming SSH key is configured)
        print_status "Copying files to instance..."
        scp -r . ec2-user@$INSTANCE_IP:/opt/chatify/
        
        # Deploy on instance
        ssh ec2-user@$INSTANCE_IP << 'EOF'
            cd /opt/chatify
            
            # Stop existing containers
            docker-compose down || true
            
            # Build and start containers
            docker-compose up -d --build
            
            # Wait for services to start
            sleep 30
            
            # Check if services are running
            docker-compose ps
EOF
        
        print_status "Application deployed successfully!"
        print_status "Access your application at:"
        echo "  🌐 App: http://$INSTANCE_IP:3001"
        echo "  🔧 Jenkins: http://$INSTANCE_IP:8080"
        echo "  📊 Nagios: http://$INSTANCE_IP:8081"
        
    else
        print_error "Instance IP not found. Please run infrastructure deployment first."
        exit 1
    fi
}

# Main deployment function
main() {
    echo "Chatify CI/CD Deployment Script"
    echo "==============================="
    
    check_requirements
    
    case "${1:-all}" in
        "infra")
            deploy_infrastructure
            ;;
        "app")
            deploy_application
            ;;
        "all")
            deploy_infrastructure
            deploy_application
            ;;
        *)
            echo "Usage: $0 [infra|app|all]"
            echo "  infra - Deploy only infrastructure"
            echo "  app   - Deploy only application"
            echo "  all   - Deploy both (default)"
            exit 1
            ;;
    esac
    
    print_status "Deployment completed! 🎉"
}

# Run main function
main "$@"