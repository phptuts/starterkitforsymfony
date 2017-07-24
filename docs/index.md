---
layout: default
title: "Starter Kit For Symfony Projects"
disqus: 1
---

# Starter Kit For Symfony Projects Setup Guide.

## Things you will need before setting up.

- Amazon s3:
    - secret
    - key
    - bucket name
    - region
- Facebook Auth
    - secret
    - client id
- Google Auth
    - secret
    - client id
- You smtp server credentials

## Setup Guide

1. Install [virtual box](https://www.virtualbox.org/).
2. Install [vagrant](https://www.vagrantup.com/).
3. Read the [homestead doc page](https://laravel.com/docs/5.4/homestead)
4. Setup Homestead environment
5. Now clone the repo for the starter kit
``` 
git clone https://github.com/phptuts/starterkitforsymfony.git
```
6. Go into your Homestead.yml file and add a site to match where you cloned the repo.  Mine looks like this.
    ``` 
    folders:
        - map: ~/vagrant/code
          to: /home/vagrant/Code
    
    sites:
        - map: bigfootlocator.app
          to: /home/vagrant/Code/Symfony/SymfonyStart/web
          type: symfony2
    ```
7. Also add a database to your config
    ``` 
    databases:
        - homestead
        - skfsp
    ```
8. vagrant up and then do a vagrant provision in the homestead folder.
9. vagrant ssh to ssh into the vagrant box.
10. cd into the directory where your project is
11. create a jwt directory in your var folder
    ``` 
    mkdir var/jwt
    ```
12. create your private key with and write down the pass phrase you used.
    ``` 
    openssl genrsa -out var/jwt/private.pem -aes256 4096
    ```
13. create your public key, you will need the pass phrase here and in the composer install step
    ``` 
    openssl rsa -pubout -in var/jwt/private.pem -out var/jwt/public.pem
    ```
14. Run  composer install
15. Composer will ask for all the stuff you setup in the pre steps + jwt pass phrase & database info.  Homestead root 
password stuff is below.  You can always change this in the parameters.yml, fyi.
here:
    ``` 
        app.database_user: homestead
        app.database_password: secret
        app.database_name: skfsp ## or whatever you named it in the config
    ```
16. run migrations and this data fixtures script to load a default user.

    ``` 
       bin/console doctrine:migrations:migrate
       
       bin/console doctrine:fixtures:load --fixtures="./src/AppBundle/DataFixtures/ORM/LoadStartUserData.php"
    ```
17. Delete the .git folder
    ```
        rm -rf .git
    ```

You should then have the website setup.  The user to login is:

email: admin@not-real-domain.com /
password: password

Then your done if you have any questions just ask!!! :)


## Running Tests

This will run all the tests 

``` 
    sh scripts/run_tests.sh
```



