---
layout: page
title: "Setup Locally Using Vagrant & Homestead"
category: tut
date: 2017-07-22 15:08:19
order: 1
disqus: 1
---

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

1. Install [virtual box]().
2. Install [vagrant]().
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
11. Run a composer install
12. Fill in all the information that you got in the pre step.  Homestead root password stuff is here:
``` 
    app.database_user: homestead
    app.database_password: secret

```

13. run migrations.
``` 
bin/console doctrine:migrations:migrate
```

Then your done if you have any questions just ask!!! :)
