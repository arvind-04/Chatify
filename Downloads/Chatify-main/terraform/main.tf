# Terraform configuration for Chatify deployment on AWS EC2

terraform {
  required_version = ">= 1.0"
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }
}

provider "aws" {
  region = var.aws_region
}

# VPC Configuration
resource "aws_vpc" "chatify_vpc" {
  cidr_block           = "10.0.0.0/16"
  enable_dns_hostnames = true
  enable_dns_support   = true

  tags = {
    Name = "chatify-vpc"
  }
}

# Internet Gateway
resource "aws_internet_gateway" "chatify_igw" {
  vpc_id = aws_vpc.chatify_vpc.id

  tags = {
    Name = "chatify-igw"
  }
}

# Public Subnet
resource "aws_subnet" "chatify_public_subnet" {
  vpc_id                  = aws_vpc.chatify_vpc.id
  cidr_block              = "10.0.1.0/24"
  availability_zone       = "${var.aws_region}a"
  map_public_ip_on_launch = true

  tags = {
    Name = "chatify-public-subnet"
  }
}

# Route Table
resource "aws_route_table" "chatify_public_rt" {
  vpc_id = aws_vpc.chatify_vpc.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.chatify_igw.id
  }

  tags = {
    Name = "chatify-public-rt"
  }
}

# Route Table Association
resource "aws_route_table_association" "chatify_public_rta" {
  subnet_id      = aws_subnet.chatify_public_subnet.id
  route_table_id = aws_route_table.chatify_public_rt.id
}

# Security Groups
resource "aws_security_group" "chatify_sg" {
  name        = "chatify-security-group"
  description = "Security group for Chatify application"
  vpc_id      = aws_vpc.chatify_vpc.id

  # SSH access
  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # HTTP access
  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # HTTPS access
  ingress {
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # Node.js app port
  ingress {
    from_port   = 3000
    to_port     = 3000
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # React app port
  ingress {
    from_port   = 3001
    to_port     = 3001
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # Jenkins port
  ingress {
    from_port   = 8080
    to_port     = 8080
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # Nagios port
  ingress {
    from_port   = 8081
    to_port     = 8081
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "chatify-sg"
  }
}

# Key Pair
resource "aws_key_pair" "chatify_key" {
  key_name   = "chatify-key"
  public_key = file(var.public_key_path)
}

# EC2 Instance
resource "aws_instance" "chatify_server" {
  ami                    = var.ami_id
  instance_type          = var.instance_type
  key_name               = aws_key_pair.chatify_key.key_name
  vpc_security_group_ids = [aws_security_group.chatify_sg.id]
  subnet_id              = aws_subnet.chatify_public_subnet.id

  user_data = file("${path.module}/user_data.sh")

  tags = {
    Name = "chatify-server"
  }
}

# Elastic IP
resource "aws_eip" "chatify_eip" {
  instance = aws_instance.chatify_server.id
  domain   = "vpc"

  tags = {
    Name = "chatify-eip"
  }
}