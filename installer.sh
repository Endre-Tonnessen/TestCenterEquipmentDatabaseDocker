#!/bin/bash
#sudo apt install docker.io # Install docker if not already available
#sudo snap install docker   # 

#sudo apt install openssh-server # Install ssh 

# Install base image
docker build -f php-nginx-base.Dockerfile -t testcenterlaerdal/database . 

# Install application image
# make build
docker-compose up -d --build

# Configure
# Laravel Artisan commands dockerdb-app-1
docker exec app-laerdal-database php artisan key:generate
docker exec app-laerdal-database php artisan storage:link


docker exec app-laerdal-database npm run prod

# Run Production caching
docker exec app-laerdal-database php artisan cache:clear
# Clear and cache config
docker exec app-laerdal-database php artisan config:cache
# Clear and cache views
docker exec app-laerdal-database php artisan view:cache

docker exec app-laerdal-database php artisan migrate # --force
docker exec app-laerdal-database php artisan db:seed # Initial admin account
# Run browser?


# Copy files from windows filesystem into the linux subsystem
# sudo cp -a 'mnt/c/Users/noeto5/OneDrive - Laerdal Medical AS/Desktop/Docker DB' home/$USER