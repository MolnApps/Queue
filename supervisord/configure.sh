# Set default directory
cd "${0%/*}"

# Copy worker configuration files to supervisor
sudo cp conf/* /etc/supervisor/conf.d/

# Reload supervisor
sudo service supervisor stop
sudo service supervisor start