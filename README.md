**CM**
======

1) Installation
---------------

  * Comment every refer to every not yet installed bundle in app/AppKernel.php, app/config/\*, src/CM/CMBundle/Resources/config/\*

  * Run `php ../composer.phar update`

  * Decomment the bundles' refers

  * Install [less][1] via [npm][2] running `npm install less --prefix app/Resources/node_modules`

  * Execute the migration `php app/console doctrine:migrations:migrate`

  * Run the fixtures `php app/console doctrine:fixtures:load -n`

2) Working with assetic
-----------------------

While working on javascripts or css files, run `php app/console assetic:dump --watch` in a separate terminal.

3) Debugging
------------

Do you want to avoid the painful experience of 500-status blank pages? Run `tail -f app/logs/<env>.log` in a separate terminal.  

[1]:  http://lesscss.org/
[2]:  http://nodejs.org/
