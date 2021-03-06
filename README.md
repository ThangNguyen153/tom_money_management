#Source: [Tutorial topic](https://kipalog.com/posts/Cai-dat-moi-truong-Docker-cho-Laravel-2019)
##Edited by: [Tom Nguyen](https://www.linkedin.com/in/thang-n-b978ba170/)

### A. This step is used to build project from zero, if you clone my git repository, so skip this and move to Section B "Run project after cloning git repository":

1. #### Build image for composer:
    `docker build -t tmm_composer -f ./docker/composer/composer.dockerfile ./docker/composer`

2. #### Run composer image to create Source Code folder ( remove container after creating successfully ):
    `docker run --rm -it -v $(pwd)/web:/srv/app/web/ tmm_composer:latest /root/.composer/vendor/bin/laravel new app`

3. #### Change owner for APP folder in your local folder (change 'thang' user to your own user ):
    `sudo chown -R thang:thang web/`

4. #### Change information in .env and app.php file:
    *Change variable in .env and app.php file in app folder*

5. #### Run composer image to install vendor ( remove container after creating successfully ):
    `docker run --rm --name tmm_composer -v $(pwd)/web/app:/srv/app/web tmm_composer:latest composer install`

6. #### Change owner for storage and bootstrap/cache/ folder in your local folder:
    `sudo chown www-data:thang -R web/app/storage/`<br/>
    `sudo chown www-data:thang -R web/app/bootstrap/cache/`<br/>

   * This will let you do some changes in storage or bootstrap cache folder when pull new code from remote branch: <br/>
     `find ./web/app/storage/ -type d -exec chmod 775 {} \;  # Change directory permissions rwxr-xr-x`<br/>
     `find ./web/app/bootstrap/cache/ -type f -exec chmod 644 {} \;  # Change file permissions rw-r--r--`<br/>

7. #### Run project:
    `docker-compose up -d`

#### URL:
    homepage: https://localhost:8443
    phpmyadmin: https://localhost:8081
### B. Run project after cloning git repository:

1. #### Build image for composer:
   `docker build -t tmm_composer -f ./docker/composer/composer.dockerfile ./docker/composer`

2. #### Change owner for APP folder in your local folder (change 'thang' user to your own user ):
   `sudo chown -R thang:thang web/`

3. #### Create .env file:
   * Moving into App folder, run this command below to create .env from .env.example file *<br/>
      `cp -r .env.example .env`

4. #### Run composer image to update vendor ( remove container after creating successfully ):
   `docker run --rm --name tmm_composer -v $(pwd)/web/app:/srv/app/web tmm_composer:latest composer update`

5. #### Change owner for storage and bootstrap/cache/ folder in your local folder:
   `sudo chown www-data:thang -R web/app/storage/`<br/>
   `sudo chown www-data:thang -R web/app/bootstrap/cache/`<br/>

   * This will let you do some changes in storage or bootstrap cache folder when pull new code from remote branch: <br/>
     `find ./web/app/storage/ -type d -exec chmod 775 {} \;  # Change directory permissions rwxr-xr-x`<br/>
     `find ./web/app/bootstrap/cache/ -type f -exec chmod 644 {} \;  # Change file permissions rw-r--r--`<br/>

6. #### Run project:
   `docker-compose up -d`
7. #### Migrate database:
   `php artisan migrate`
#### URL:
    homepage: https://localhost:8443
    phpmyadmin: http://localhost:8444

#### APP:
   * Access App container: `docker-compose exec app bash`
   * create user:<br/>
      * `php artisan tinker`<br/>
      * `DB::table('users')->insert(['username'=>'admin','firstname'=>'thang','lastname'=>'nguyen','fullname'=>'thangnguyen','email'=>'xxx@gmail.com','password'=>Hash::make('123456')])`<br/>
   * create client: `php artisan passport:install`
   * Import some default info: `php artisan db:seed CLASS_NAME`

#### Notice:
1. Update composer: 
    * Remove current composer image
    * Edit docker/composer.dockerfile
    * Build new composer image:<br/>
      `docker build -t tmm_composer -f ./docker/composer/composer.dockerfile ./docker/composer`
    * Update vendor:<br/>
      `docker run --rm --name tmm_composer -v $(pwd)/web/app:/srv/app/web tmm_composer:latest composer update`
2. Install new package from composer (Please make sure that you add package config into app.php file manually):<br/>
   `docker run --rm --name tmm_composer -v $(pwd)/web/app:/srv/app/web tmm_composer:latest composer require <your package name>`
   * Add class path of the package to config/app.php

#### Change logs: