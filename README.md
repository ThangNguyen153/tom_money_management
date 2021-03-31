https://kipalog.com/posts/Cai-dat-moi-truong-Docker-cho-Laravel-2019

docker build -t localcomposer -f ./composer/composer.dockerfile ./composer

docker run -it -v $(pwd):/var/www/html localcomposer:latest /root/.composer/vendor/bin/laravel new app

docker run -v $(pwd)/app:/var/www/html localcomposer:latest composer install

change variable in .env file in app folder

docker-compose up -d