Ositel test
================

### Installation
```sh
$ composer install
$ php bin/console doctrine:database:create
$ php bin/console doctrine:schema:update -f
$ php bin/console doctrine:fixtures:load -n
$ php bin/console cache:clear
```
