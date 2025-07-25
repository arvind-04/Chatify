# 🚀 Adding CI/CD Pipeline to Your GitHub Repository

This guide will help you add the complete CI/CD infrastructure to your existing Chatify GitHub repository.

## 📋 Pre-Commit Checklist

### 1. Repository Structure
Your repository should now have this structure:
```
your-chatify-repo/
├── .github/
│   └── workflows/              # GitHub Actions (CI/CD)
│       ├── ci.yml             # Continuous Integration
│       └── cd.yml             # Continuous Deployment
├── Chatify/                   # Your existing app
│   ├── frontend/              # React app
│   ├── server/               # Node.js backend
│   ├── Dockerfile            # App containerization
│   ├── docker-compose.yml    # Services orchestration
│   └── Jenkinsfile          # Jenkins pipeline
├── infrastructure/           # DevOps configuration
│   ├── terraform/           # AWS infrastructure
│   ├── nagios/             # Monitoring setup
│   ├── scripts/            # Deployment scripts
│   └── github-actions/     # Workflow templates
├── .dockerignore           # Docker ignore rules
├── deploy.sh              # Main deployment script
├── README.md              # Updated documentation
└── SETUP-GITHUB.md        # This file
```

### 2. Required GitHub Secrets
Before the CI/CD pipeline can work, add these secrets to your GitHub repository:

**Go to: Repository → Settings → Secrets and variables → Actions**

Add these secrets:
- `AWS_ACCESS_KEY_ID` - Your AWS access key
- `AWS_SECRET_ACCESS_KEY` - Your AWS secret key  
- `EC2_SSH_KEY` - Your private SSH key content

### 3. Get Your SSH Key
```bash
# Generate if you don't have one
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa

# Copy private key content for GitHub secret
cat ~/.ssh/id_rsa

# Copy public key content for Terraform
cat ~/.ssh/id_rsa.pub
```

## 🔧 Setup Steps

### Step 1: Commit to GitHub
```bash
# Add all files
git add .

# Commit with descriptive message
git commit -m "Add complete CI/CD pipeline with Terraform, Jenkins, and Nagios

- Add GitHub Actions for CI/CD automation
- Add Terraform for AWS infrastructure deployment
- Add Docker containerization with multi-stage builds
- Add Jenkins pipeline for continuous deployment
- Add Nagios monitoring and health checks
- Add comprehensive deployment scripts
- Update project structure and documentation"

# Push to your repository
git push origin main
```

### Step 2: Configure GitHub Secrets
1. Go to your repository on GitHub
2. Click **Settings** → **Secrets and variables** → **Actions**
3. Click **New repository secret** and add:

**AWS_ACCESS_KEY_ID**
```
Your AWS access key ID
```

**AWS_SECRET_ACCESS_KEY**
```
Your AWS secret access key
```

**EC2_SSH_KEY**
```
-----BEGIN OPENSSH PRIVATE KEY-----
[Your private key content from ~/.ssh/id_rsa]
-----END OPENSSH PRIVATE KEY-----
```

### Step 3: Configure AWS Credentials Locally
```bash
aws configure
```
Enter:
- AWS Access Key ID: [Your key]
- AWS Secret Access Key: [Your secret]
- Default region: us-east-1
- Default output format: json

### Step 4: Update Terraform Variables (Optional)
Edit `infrastructure/terraform/variables.tf` if you want to change:
- AWS region
- Instance type
- AMI ID

## 🚀 Deployment Options

### Option 1: GitHub Actions (Recommended)
- **Automatic**: Push to `main` branch triggers deployment
- **Manual**: Go to Actions tab → CD Pipeline → Run workflow

### Option 2: Local Deployment
```bash
# Interactive menu
./deploy.sh

# Or direct execution
infrastructure/scripts/execute-deployment.sh
```

### Option 3: Step-by-Step Manual
```bash
# 1. Setup prerequisites
infrastructure/scripts/setup-prerequisites.sh

# 2. Deploy infrastructure
cd infrastructure/terraform
terraform init
terraform plan
terraform apply

# 3. Deploy application
cd ../scripts
./execute-deployment.sh
```

## 📊 What Gets Deployed

### AWS Infrastructure
- **VPC** with public subnet
- **EC2 instance** (t3.medium) with Docker
- **Security groups** for web traffic
- **Elastic IP** for consistent access

### Applications & Services
- **Chatify App** on port 3001
- **Jenkins** on port 8080
- **Nagios** on port 8081
- **Nginx** reverse proxy

### Monitoring & CI/CD
- **GitHub Actions** for automated CI/CD
- **Jenkins** for continuous deployment
- **Nagios** for health monitoring
- **Docker** containerization

## 🔍 After Deployment

### Access Your Services
After successful deployment, you'll get URLs like:
- **App**: `http://[instance-ip]:3001`
- **Jenkins**: `http://[instance-ip]:8080`
- **Nagios**: `http://[instance-ip]:8081` (admin/admin123)

### Monitor Deployment
- **GitHub Actions**: Repository → Actions tab
- **AWS Console**: Check EC2 instances
- **Local logs**: `docker-compose logs`

### Health Checks
```bash
# Remote health check
ssh ec2-user@[instance-ip] "cd /opt/chatify && ./infrastructure/scripts/health-check.sh"

# Local health check
infrastructure/scripts/health-check.sh
```

## 🛠️ Troubleshooting

### Common Issues

**1. GitHub Actions Failing**
- Check repository secrets are set correctly
- Verify AWS credentials have proper permissions
- Check workflow logs in Actions tab

**2. Terraform Errors**
- Ensure AWS CLI is configured: `aws sts get-caller-identity`
- Check if resources already exist in AWS
- Verify SSH key path in variables.tf

**3. Docker Issues**
- Start Docker Desktop: `open -a Docker`
- Check Docker daemon: `docker info`
- Verify container status: `docker ps`

**4. SSH Connection Issues**
- Verify SSH key permissions: `chmod 600 ~/.ssh/id_rsa`
- Check security group allows SSH (port 22)
- Wait for instance to fully boot (2-3 minutes)

### Getting Help
- Check `infrastructure/DEPLOYMENT.md` for detailed docs
- View container logs: `docker-compose logs [service-name]`
- SSH into instance: `ssh ec2-user@[instance-ip]`
- Check AWS CloudWatch for instance logs

## 🧹 Cleanup

To remove all AWS resources:
```bash
infrastructure/scripts/execute-deployment.sh
# Select option 7 (Cleanup Resources)
```

Or manually:
```bash
cd infrastructure/terraform
terraform destroy
```

---

**🎉 You're all set!** Your Chatify app now has a complete CI/CD pipeline with infrastructure as code, automated testing, and monitoring!