# Chatify CI/CD Deployment Guide

This guide covers the complete CI/CD pipeline setup for the Chatify real-time chat application, including infrastructure deployment, containerization, and monitoring.

## Architecture Overview

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   GitHub        │    │   Jenkins       │    │   AWS EC2       │
│   Actions       │───▶│   Pipeline      │───▶│   Instance      │
│   (CI/CD)       │    │   (Docker)      │    │   (Production)  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                                       │
                                               ┌───────▼───────┐
                                               │   Monitoring   │
                                               │   (Nagios)     │
                                               └───────────────┘
```

## Prerequisites

### Required Tools
- **Terraform** (>= 1.0)
- **Docker** & **Docker Compose**
- **Node.js** (>= 18)
- **AWS CLI** (configured)
- **Git**

### AWS Setup
1. Create AWS account and configure credentials
2. Generate EC2 key pair
3. Set up IAM user with appropriate permissions

### GitHub Secrets
Configure the following secrets in your GitHub repository:
- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`
- `EC2_SSH_KEY` (private key content)

## Quick Start

### 1. Clone and Setup
```bash
git clone <your-repo-url>
cd chatify
```

### 2. Deploy Infrastructure
```bash
# Deploy everything
./deploy.sh

# Or deploy step by step
./deploy.sh infra  # Infrastructure only
./deploy.sh app    # Application only
```

### 3. Access Services
After deployment, access your services at:
- **Application**: `http://<instance-ip>:3001`
- **Jenkins**: `http://<instance-ip>:8080`
- **Nagios**: `http://<instance-ip>:8081`

## Detailed Setup

### Infrastructure (Terraform)

The Terraform configuration creates:
- VPC with public subnet
- Security groups with appropriate ports
- EC2 instance with Docker pre-installed
- Elastic IP for static access

```bash
cd terraform
terraform init
terraform plan
terraform apply
```

### Application Deployment

The application uses a multi-stage Docker build:
1. **Frontend Build**: React app compilation
2. **Backend Setup**: Node.js server with Socket.IO
3. **Production Image**: Optimized final image

```bash
docker-compose up -d --build
```

### CI/CD Pipeline

#### GitHub Actions
- **CI Pipeline** (`ci.yml`): Runs on every push/PR
  - Linting (ESLint)
  - Testing (Jest)
  - Security audits
  - Docker build testing

- **CD Pipeline** (`cd.yml`): Runs on main branch
  - Infrastructure deployment
  - Application deployment
  - Health checks

#### Jenkins Pipeline
- Automated builds on code changes
- Parallel testing and linting
- Docker image creation
- Deployment to production
- Health monitoring

### Monitoring (Nagios)

Nagios monitors:
- **Application Health**: HTTP checks on port 3000
- **Socket.IO Service**: TCP checks
- **System Resources**: CPU, memory, disk
- **Docker Services**: Container status

Default credentials:
- Username: `admin`
- Password: `admin123`

## Configuration Files

### Key Files Structure
```
├── .github/workflows/          # GitHub Actions
│   ├── ci.yml                 # Continuous Integration
│   └── cd.yml                 # Continuous Deployment
├── terraform/                 # Infrastructure as Code
│   ├── main.tf               # Main Terraform config
│   ├── variables.tf          # Variables
│   ├── outputs.tf            # Outputs
│   └── user_data.sh          # EC2 initialization
├── nagios/config/            # Monitoring configuration
│   ├── chatify.cfg          # Service definitions
│   └── commands.cfg         # Custom commands
├── Chatify/
│   ├── Dockerfile           # Multi-stage build
│   ├── frontend/            # React application
│   └── server/              # Node.js backend
├── docker-compose.yml       # Container orchestration
├── Jenkinsfile             # Jenkins pipeline
└── deploy.sh               # Deployment script
```

## Environment Variables

### Application
- `NODE_ENV=production`
- `PORT=3000`

### Jenkins
- `JENKINS_OPTS=--httpPort=8080`

### Nagios
- `NAGIOSADMIN_USER=admin`
- `NAGIOSADMIN_PASS=admin123`

## Security Considerations

1. **Network Security**
   - Security groups restrict access to necessary ports
   - VPC isolation

2. **Application Security**
   - Regular dependency audits
   - Non-root container execution
   - Health checks implemented

3. **Access Control**
   - SSH key-based authentication
   - IAM roles and policies
   - Service-specific credentials

## Troubleshooting

### Common Issues

#### 1. Terraform Deployment Fails
```bash
# Check AWS credentials
aws sts get-caller-identity

# Verify Terraform state
terraform state list
```

#### 2. Docker Build Issues
```bash
# Check Docker daemon
docker info

# View container logs
docker-compose logs chatify-app
```

#### 3. Application Not Accessible
```bash
# Check security groups
aws ec2 describe-security-groups

# Verify container status
docker ps
```

#### 4. Jenkins Setup Issues
```bash
# Get initial admin password
docker exec chatify-jenkins cat /var/jenkins_home/secrets/initialAdminPassword
```

### Health Checks

```bash
# Application health
curl http://<instance-ip>:3001

# Jenkins health
curl http://<instance-ip>:8080

# Nagios health
curl http://<instance-ip>:8081
```

## Scaling and Optimization

### Horizontal Scaling
- Use Application Load Balancer
- Multiple EC2 instances
- Auto Scaling Groups

### Performance Optimization
- CloudFront CDN for static assets
- ElastiCache for session storage
- RDS for persistent data

### Cost Optimization
- Use spot instances for development
- Implement auto-shutdown schedules
- Monitor resource utilization

## Maintenance

### Regular Tasks
1. **Security Updates**
   - Update base images monthly
   - Apply OS patches
   - Review dependency vulnerabilities

2. **Monitoring**
   - Check Nagios alerts
   - Review application logs
   - Monitor resource usage

3. **Backup**
   - Database backups (if applicable)
   - Configuration backups
   - Code repository maintenance

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review application logs
3. Check monitoring dashboards
4. Create GitHub issues for bugs

---

**Note**: This setup is designed for demonstration and development purposes. For production use, consider additional security hardening, backup strategies, and compliance requirements.