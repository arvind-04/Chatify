#!/bin/bash

# Main execution script for Chatify deployment
set -e

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_header() {
    echo -e "${BLUE}================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}================================${NC}"
}

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

show_menu() {
    print_header "Chatify CI/CD Deployment Options"
    echo "1. 🏠 Local Development Setup"
    echo "2. 🧪 Test Everything Locally"
    echo "3. 🚀 Deploy Infrastructure Only"
    echo "4. 📦 Deploy Application Only"
    echo "5. 🌟 Full Production Deployment"
    echo "6. 🔍 Health Check"
    echo "7. 🧹 Cleanup Resources"
    echo "8. ❓ Help & Documentation"
    echo "0. Exit"
    echo
}

local_development() {
    print_header "Local Development Setup"
    ./local-dev.sh
}

test_locally() {
    print_header "Testing Locally"
    
    print_status "Starting local test environment..."
    
    # Start services locally
    cd ../../Chatify
    docker-compose up -d
    cd ../infrastructure/scripts
    
    print_status "Waiting for services to start..."
    sleep 30
    
    # Run health check
    ./scripts/health-check.sh
    
    print_status "Local testing completed!"
    print_warning "Services are running. Use 'docker-compose down' to stop them."
}

deploy_infrastructure() {
    print_header "Infrastructure Deployment"
    
    print_status "Deploying AWS infrastructure with Terraform..."
    
    cd ../terraform
    
    # Check if terraform is initialized
    if [ ! -d ".terraform" ]; then
        print_status "Initializing Terraform..."
        terraform init
    fi
    
    # Plan
    print_status "Creating deployment plan..."
    terraform plan -out=tfplan
    
    # Confirm deployment
    read -p "Do you want to apply this plan? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        terraform apply tfplan
        
        # Save outputs
        terraform output -json > ../terraform-outputs.json
        INSTANCE_IP=$(terraform output -raw instance_public_ip)
        echo "$INSTANCE_IP" > ../instance_ip.txt
        
        print_status "Infrastructure deployed successfully!"
        print_status "Instance IP: $INSTANCE_IP"
    else
        print_warning "Deployment cancelled."
    fi
    
    cd ../scripts
}

deploy_application() {
    print_header "Application Deployment"
    
    if [ ! -f "instance_ip.txt" ]; then
        print_error "No instance IP found. Please deploy infrastructure first."
        return 1
    fi
    
    INSTANCE_IP=$(cat instance_ip.txt)
    print_status "Deploying to instance: $INSTANCE_IP"
    
    # Wait for instance to be ready
    print_status "Waiting for instance to be ready..."
    for i in {1..30}; do
        if ssh -o ConnectTimeout=5 -o StrictHostKeyChecking=no ec2-user@$INSTANCE_IP "echo 'ready'" 2>/dev/null; then
            break
        fi
        echo -n "."
        sleep 10
    done
    echo
    
    # Copy files and deploy
    print_status "Copying files to instance..."
    cd ../../
    rsync -avz --exclude='.git' --exclude='infrastructure/terraform/.terraform' . ec2-user@$INSTANCE_IP:/opt/chatify/
    
    print_status "Starting deployment on instance..."
    ssh ec2-user@$INSTANCE_IP << 'EOF'
        cd /opt/chatify
        
        # Make scripts executable
        chmod +x deploy.sh infrastructure/scripts/health-check.sh
        
        # Stop existing containers
        cd Chatify
        docker-compose down || true
        
        # Build and start containers
        docker-compose up -d --build
        
        # Wait for services
        sleep 60
        
        # Run health check
        cd ../infrastructure/scripts
        ./health-check.sh
EOF
    
    print_status "Application deployed successfully!"
    show_urls
}

full_deployment() {
    print_header "Full Production Deployment"
    
    print_status "Starting complete deployment process..."
    
    # Deploy infrastructure
    deploy_infrastructure
    
    # Deploy application
    deploy_application
    
    print_status "Full deployment completed! 🎉"
}

health_check() {
    print_header "Health Check"
    
    if [ -f "instance_ip.txt" ]; then
        INSTANCE_IP=$(cat instance_ip.txt)
        print_status "Running health check on instance: $INSTANCE_IP"
        
        ssh ec2-user@$INSTANCE_IP "cd /opt/chatify && ./infrastructure/scripts/health-check.sh"
        
        show_urls
    else
        print_status "Running local health check..."
        ./health-check.sh
    fi
}

cleanup_resources() {
    print_header "Cleanup Resources"
    
    print_warning "This will destroy all AWS resources!"
    read -p "Are you sure you want to continue? (y/N): " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        cd ../terraform
        terraform destroy -auto-approve
        cd ../scripts
        
        # Clean up local files
        rm -f instance_ip.txt terraform-outputs.json
        
        print_status "Resources cleaned up successfully!"
    else
        print_status "Cleanup cancelled."
    fi
}

show_urls() {
    if [ -f "instance_ip.txt" ]; then
        INSTANCE_IP=$(cat instance_ip.txt)
        print_status "Access your services at:"
        echo "  🌐 Chatify App: http://$INSTANCE_IP:3001"
        echo "  🔧 Jenkins: http://$INSTANCE_IP:8080"
        echo "  📊 Nagios: http://$INSTANCE_IP:8081 (admin/admin123)"
        echo "  🖥️  SSH Access: ssh ec2-user@$INSTANCE_IP"
    fi
}

show_help() {
    print_header "Help & Documentation"
    echo "📖 Available Documentation:"
    echo "  • DEPLOYMENT.md - Complete deployment guide"
    echo "  • github-setup.md - GitHub configuration"
    echo "  • README.md - Application overview"
    echo
    echo "🔧 Useful Commands:"
    echo "  • ./local-dev.sh - Set up local development"
    echo "  • ./scripts/health-check.sh - Check service health"
    echo "  • docker-compose logs - View container logs"
    echo "  • terraform output - View infrastructure outputs"
    echo
    echo "🆘 Troubleshooting:"
    echo "  • Check AWS credentials: aws sts get-caller-identity"
    echo "  • Verify Docker: docker info"
    echo "  • Check SSH key: ls -la ~/.ssh/"
    echo "  • View logs: docker-compose logs [service-name]"
}

# Main execution
main() {
    while true; do
        show_menu
        read -p "Select an option (0-8): " choice
        echo
        
        case $choice in
            1) local_development ;;
            2) test_locally ;;
            3) deploy_infrastructure ;;
            4) deploy_application ;;
            5) full_deployment ;;
            6) health_check ;;
            7) cleanup_resources ;;
            8) show_help ;;
            0) 
                print_status "Goodbye! 👋"
                exit 0
                ;;
            *)
                print_error "Invalid option. Please try again."
                ;;
        esac
        
        echo
        read -p "Press Enter to continue..."
        echo
    done
}

# Check if script is run directly
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi