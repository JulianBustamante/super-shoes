GAP: Super Shoes
================

Installation
------------
1. Clone the repository
    `git clone git clone git@bitbucket.org:julianbustamante/super-shoes.git`

2. Download dependencies through composer
    `composer install`

3. Create the database schema use:
    `php bin/console doctrine:schema:create`

4. Create the fixtures data use:
    `php bin/console doctrine:fixtures:load`

Api
---
You can browse the whole API documentation at: http://yourdomain/services/doc

Run the test
------------
To run the tests install PHPUnit 3.7+ and call:
    `phpunit -c app/`
