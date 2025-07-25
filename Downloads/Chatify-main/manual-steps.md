# Manual Step-by-Step Execution

## Phase 1: Prerequisites
```bash
# 1. Install tools
./setup-prerequisites.sh

# 2. Configure AWS
aws configure

# 3. Start Docker Desktop
open -a Docker

# 4. Verify setup
terraform --version
docker --version
aws sts get-caller-identity
```

## Phase 2: Local Testing
```bash
# 1. Test locally first
./local-dev.sh

# 2. Run local containers
docker-compose up -d

# 3. Check health
./scripts/health-check.sh

# 4. Stop local containers
docker-compose down
```

## Phase 3: Infrastructure Deployment
```bash
# 1. Deploy AWS infrastructure
cd terraform
terraform init
terraform plan
terraform apply

# 2. Get instance IP
terraform output instance_public_ip

# 3. Save IP for later use
terraform output -raw instance_public_ip > ../instance_ip.txt
cd ..
```

## Phase 4: Application Deployment
```bash
# 1. Get instance IP
INSTANCE_IP=$(cat instance_ip.txt)

# 2. Copy files to instance
rsync -avz . ec2-user@$INSTANCE_IP:/opt/chatify/

# 3. Deploy on instance
ssh ec2-user@$INSTANCE_IP
cd /opt/chatify
docker-compose up -d --build
```

## Phase 5: Verification
```bash
# 1. Check services
./scripts/health-check.sh

# 2. Access applications
# App: http://<instance-ip>:3001
# Jenkins: http://<instance-ip>:8080  
# Nagios: http://<instance-ip>:8081
```

## Phase 6: Monitoring Setup
```bash
# 1. Access Jenkins
# Get initial password: docker exec chatify-jenkins cat /var/jenkins_home/secrets/initialAdminPassword

# 2. Configure Jenkins pipeline
# - Install suggested plugins
# - Create pipeline job
# - Point to your GitHub repository

# 3. Access Nagios
# Username: admin
# Password: admin123
```