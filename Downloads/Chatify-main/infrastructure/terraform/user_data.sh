#!/bin/bash

# Update system
yum update -y

# Install Docker
yum install -y docker
systemctl start docker
systemctl enable docker
usermod -a -G docker ec2-user

# Install Docker Compose
curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# Install Node.js and npm
curl -fsSL https://rpm.nodesource.com/setup_18.x | bash -
yum install -y nodejs

# Install Git
yum install -y git

# Create application directory
mkdir -p /opt/chatify
chown ec2-user:ec2-user /opt/chatify

# Create Jenkins directory
mkdir -p /opt/jenkins
chown ec2-user:ec2-user /opt/jenkins

# Create Nagios directory
mkdir -p /opt/nagios
chown ec2-user:ec2-user /opt/nagios

# Install nginx for reverse proxy
yum install -y nginx
systemctl start nginx
systemctl enable nginx

# Create nginx configuration for the app
cat > /etc/nginx/conf.d/chatify.conf << 'EOF'
server {
    listen 3001;
    server_name _;

    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }
}
EOF

systemctl reload nginx

echo "User data script completed" > /var/log/user-data.log