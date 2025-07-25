#!/bin/bash

# Health check script for Chatify application
set -e

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Configuration
APP_URL="http://localhost:3000"
JENKINS_URL="http://localhost:8080"
NAGIOS_URL="http://localhost:8081"

print_status() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

check_service() {
    local service_name=$1
    local url=$2
    local expected_status=${3:-200}
    
    echo -n "Checking $service_name... "
    
    if curl -s -o /dev/null -w "%{http_code}" "$url" | grep -q "$expected_status"; then
        print_status "$service_name is healthy"
        return 0
    else
        print_error "$service_name is not responding"
        return 1
    fi
}

check_docker_containers() {
    echo "Checking Docker containers..."
    
    containers=("chatify-app" "chatify-jenkins" "chatify-nagios")
    
    for container in "${containers[@]}"; do
        if docker ps --format "table {{.Names}}" | grep -q "$container"; then
            print_status "$container is running"
        else
            print_error "$container is not running"
        fi
    done
}

main() {
    echo "Chatify Health Check"
    echo "==================="
    
    # Check Docker containers
    check_docker_containers
    echo
    
    # Check services
    check_service "Chatify App" "$APP_URL"
    check_service "Jenkins" "$JENKINS_URL"
    check_service "Nagios" "$NAGIOS_URL"
    
    echo
    echo "Health check completed!"
}

main "$@"