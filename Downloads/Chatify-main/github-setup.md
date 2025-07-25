# GitHub Repository Setup

## 1. Create GitHub Repository
1. Go to GitHub and create a new repository
2. Clone this code to your repository
3. Push the code to your GitHub repository

## 2. Configure GitHub Secrets
Go to your repository → Settings → Secrets and variables → Actions

Add these secrets:

### Required Secrets:
- **AWS_ACCESS_KEY_ID**: Your AWS access key
- **AWS_SECRET_ACCESS_KEY**: Your AWS secret key
- **EC2_SSH_KEY**: Your private SSH key content

### Get your SSH private key:
```bash
cat ~/.ssh/id_rsa
```
Copy the entire content (including -----BEGIN and -----END lines)

## 3. Enable GitHub Actions
1. Go to your repository → Actions tab
2. Enable GitHub Actions if not already enabled
3. The workflows will trigger automatically on push to main branch

## 4. Repository Structure
Make sure your repository has this structure:
```
your-repo/
├── .github/workflows/
├── terraform/
├── Chatify/
├── nagios/
├── docker-compose.yml
├── Jenkinsfile
└── deploy.sh
```