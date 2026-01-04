#!/bin/bash
# Script to help automate (mostly) initial host server setup

# Check if the correct number of arguments is provided
if [ "$#" -ne 2 ]; then
    echo "Usage: $0 <username> <apikey>"
    exit 1
fi

# Assign arguments to variables
USERNAME=$1
APIKEY=$2

# Add user to sudoers with passwordless sudo (for convenience)
(echo "$USERNAME ALL=(ALL:ALL) NOPASSWD: ALL") | sudo EDITOR='tee -a' visudo

# Disable auto-updates. Applying them manually periodically for extra control over environment
sudo systemctl stop unattended-upgrades
sudo apt-get purge unattended-upgrades

# Open ports
sudo ufw allow 80/tcp && sudo ufw allow 443/tcp && sudo ufw allow 443/udp && sudo ufw allow 22/tcp

# Enable firewall
sudo ufw --force enable

# Add Docker's official GPG key
echo "Adding docker GPG key..."
sudo apt-get update
sudo apt-get install -y ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

# Add the repository to Apt sources
echo "Adding Docker Repository..."
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "${UBUNTU_CODENAME:-$VERSION_CODENAME}") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo apt-get update

# Update system packages
echo "Updating system packages..."
sudo apt-get upgrade -y

# Install required packages
echo "Installing Docker..."
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Write custom config for Docker
echo "Writing custom config for Docker..."
sudo tee /etc/docker/daemon.json > /dev/null <<'EOF'
{
  "default-ulimits": {
    "nofile": {
      "Name": "nofile",
      "Hard": 4194304,
      "Soft": 4194304
    }
  }
}
EOF

# Set open files limits
sudo sh -c 'echo "root soft nofile 4194304" >> /etc/security/limits.conf'
sudo sh -c 'echo "root hard nofile 4194304" >> /etc/security/limits.conf'
sudo sh -c 'echo "fs.nr_open=4194304" >> /etc/sysctl.conf'

# Installing firewall bouncer for CrowdSec
# Logs are located in /var/log/crowdsec-firewall-bouncer.log
echo "Installing CrowdSec firewall bouncer..."
curl -s https://packagecloud.io/install/repositories/crowdsec/crowdsec/script.deb.sh | sudo bash
sudo apt install -y crowdsec-firewall-bouncer-iptables

# Replace API key in CrowdSec configuration
sudo sed -i "s/api_key:.*/api_key: $APIKEY/g" /etc/crowdsec/bouncers/crowdsec-firewall-bouncer.yaml
sudo sed -i 's/listen_port: 60601/listen_port: 6060/g' /etc/crowdsec/bouncers/crowdsec-firewall-bouncer.yaml
sudo sh -c 'echo "daemonize: true" >> /etc/crowdsec/bouncers/crowdsec-firewall-bouncer.yaml'
sudo sh -c 'echo "ipset_type: nethash" >> /etc/crowdsec/bouncers/crowdsec-firewall-bouncer.yaml'

# Install nano (I just have a bunch of guides for myself using it, no real reason to change that), unzip and rsync
sudo apt-get install -y nano rsync unzip

# Add these lines to /etc/sysctl.conf for UDP tuning (https://github.com/quic-go/quic-go/wiki/UDP-Buffer-Sizes)
echo "Tuning UDP for HTTP3..."
sudo sh -c 'echo "net.core.rmem_max=7500000" >> /etc/sysctl.conf'
sudo sh -c 'echo "net.core.wmem_max=7500000" >> /etc/sysctl.conf'

# Limit journal size
echo "Limiting journal size..."
echo "Uncommenting lines..."
sudo sed -i 's/#SystemMaxUse=/SystemMaxUse=500M/g' /etc/systemd/journald.conf
sudo sed -i 's/#SystemKeepFree=/SystemKeepFree=1G/g' /etc/systemd/journald.conf
sudo sed -i 's/#MaxRetentionSec=/MaxRetentionSec=100d/g' /etc/systemd/journald.conf

echo "Setting values..."
sudo sed -i 's/SystemMaxUse=.*/SystemMaxUse=500M/g' /etc/systemd/journald.conf
sudo sed -i 's/SystemKeepFree=.*/SystemKeepFree=1G/g' /etc/systemd/journald.conf
sudo sed -i 's/MaxRetentionSec=.*/MaxRetentionSec=100d/g' /etc/systemd/journald.conf

echo "Restarting service..."
sudo systemctl restart systemd-journald

echo "Updating SSH settings..."
sudo sed -i 's/#LoginGraceTime.*/LoginGraceTime 1m/g' /etc/ssh/sshd_config
sudo sed -i 's/LoginGraceTime.*/LoginGraceTime 1m/g' /etc/ssh/sshd_config
sudo sed -i 's/#MaxAuthTries.*/MaxAuthTries 6/g' /etc/ssh/sshd_config
sudo sed -i 's/MaxAuthTries.*/MaxAuthTries 6/g' /etc/ssh/sshd_config
sudo sed -i 's/#AllowAgentForwarding.*/AllowAgentForwarding no/g' /etc/ssh/sshd_config
sudo sed -i 's/AllowAgentForwarding.*/AllowAgentForwarding no/g' /etc/ssh/sshd_config
sudo sed -i 's/#X11Forwarding.*/X11Forwarding no/g' /etc/ssh/sshd_config
sudo sed -i 's/X11Forwarding.*/X11Forwarding no/g' /etc/ssh/sshd_config
sudo sed -i 's/#PrintLastLog.*/PrintLastLog no/g' /etc/ssh/sshd_config
sudo sed -i 's/PrintLastLog.*/PrintLastLog no/g' /etc/ssh/sshd_config
sudo sed -i 's/#ClientAliveInterval.*/ClientAliveInterval 300/g' /etc/ssh/sshd_config
sudo sed -i 's/ClientAliveInterval.*/ClientAliveInterval 300/g' /etc/ssh/sshd_config
sudo sed -i 's/#ClientAliveCountMax.*/ClientAliveCountMax 3/g' /etc/ssh/sshd_config
sudo sed -i 's/ClientAliveCountMax.*/ClientAliveCountMax 3/g' /etc/ssh/sshd_config
sudo sed -i 's/#MaxStartups.*/MaxStartups 10/g' /etc/ssh/sshd_config
sudo sed -i 's/MaxStartups.*/MaxStartups 10/g' /etc/ssh/sshd_config
sudo sed -i 's/#AllowUsers.*/AllowUsers administrator/g' /etc/ssh/sshd_config
sudo sed -i 's/AllowUsers.*/AllowUsers administrator/g' /etc/ssh/sshd_config

echo "Creating webserver folder..."
sudo mkdir /usr/local/webserver
sudo chown -R -L $USERNAME:$USERNAME /usr/local/webserver

# Cleanup
echo "Cleaning up..."
sudo apt autoremove -y
sudo apt autoclean -y

echo "Setup completed successfully!"