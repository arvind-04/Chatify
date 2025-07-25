output "instance_public_ip" {
  description = "Public IP address of the EC2 instance"
  value       = aws_eip.chatify_eip.public_ip
}

output "instance_public_dns" {
  description = "Public DNS name of the EC2 instance"
  value       = aws_instance.chatify_server.public_dns
}

output "jenkins_url" {
  description = "Jenkins URL"
  value       = "http://${aws_eip.chatify_eip.public_ip}:8080"
}

output "nagios_url" {
  description = "Nagios URL"
  value       = "http://${aws_eip.chatify_eip.public_ip}:8081"
}

output "app_url" {
  description = "Application URL"
  value       = "http://${aws_eip.chatify_eip.public_ip}:3001"
}