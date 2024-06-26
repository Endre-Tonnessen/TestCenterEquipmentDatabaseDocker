#!/bin/bash
#sudo apt install docker.io # Install docker if not already available
#sudo snap install docker   # 

#sudo apt install openssh-server # Install ssh 

sudo chmod 666 /var/run/docker.sock 

# Install base image
docker build -f php-nginx-base.Dockerfile -t testcenterlaerdal/database . 

# Install application image
# make build
docker-compose up -d --build

# Configure
docker exec app-laerdal-database cp .env.example .env
# docker exec app-laerdal-dataase chown -R 777 /var/www
# sudo touch /var/app/current/storage/logs/laravel.log
# sudo chown $USER:www-data /var/app/current/storage/logs/laravel.log

# Laravel Artisan commands dockerdb-app-1
docker exec app-laerdal-database php artisan key:generate
docker exec app-laerdal-database php artisan storage:link

# docker exec app-laerdal-database npm install --omit=dev
# docker exec app-laerdal-database npm cache clean --force
# docker exec app-laerdal-database npm install -g  --unsafe-perm=true --allow-root cypress
docker exec app-laerdal-database chown -R 1000:1000 "/tmp"
docker exec app-laerdal-database chown -R 82:82 "/tmp"
# docker exec app-laerdal-database npm install --production

docker exec app-laerdal-database chown -R root /var/www/app

# Run Production caching
docker exec app-laerdal-database php artisan cache:clear
# Clear and cache config
docker exec app-laerdal-database php artisan config:cache
# Clear and cache views
docker exec app-laerdal-database php artisan view:cache

# docker exec app-laerdal-database php artisan db:seed # Initial admin account
# Run browser?
sudo chown -R $USER:$USER .
docker exec app-laerdal-database npm uninstall cypress
docker exec app-laerdal-database npm install
docker exec app-laerdal-database npm run prod


docker exec app-laerdal-database chown -R www-data:www-data .

docker exec app-laerdal-database php artisan migrate:refresh --seed # --force


# docker exec app-laerdal-database chmod -R +rwX .


# Copy files from windows filesystem into the linux subsystem
# sudo cp -a 'mnt/c/Users/noeto5/OneDrive - Laerdal Medical AS/Desktop/Docker DB' home/$USER