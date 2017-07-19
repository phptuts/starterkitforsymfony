#!/usr/bin/env bash

php bin/console doctrine:database:drop --env=test --force
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:update --env=test --force
php bin/console doctrine:fixtures:load --env=test --no-interaction
