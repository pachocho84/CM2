**CM**
======

1) Installation
---------------

  * Comment every refer to every not yet installed bundle in app/AppKernel.php, app/config/\*, src/CM/CMBundle/Resources/config/\*

  * Run `php ../composer.phar update`

  * Decomment the bundles' refers

  * Install [less][1] via [npm][2] running `npm install less --prefix app/Resources/node_modules`

  * Install [wkhtmltopdf][3] via doc. Installation endpoint needed in "/usr/bin/wkhtmltopdf"

  * Add the cronjobs `sudo ./install-cronjobs.sh`

  * Execute the migration `php app/console doctrine:migrations:migrate`

  * Run the fixtures `php app/console doctrine:fixtures:load -n`

2) Working with assetic
-----------------------

While working on javascripts or css files, run `php app/console assetic:dump` to dump new generated files. They will be automatically watched.

3) Debugging
------------

Do you want to avoid the painful experience of 500-HTTP status blank pages? Run `tail -f app/logs/<env>.log` in a separate terminal.
If you want to remove also verbose junk use `rm -fr app/logs/<env>.log; touch -f app/logs/<env>.log; tail -f app/logs/<env>.log | grep -vE 'event.DEBUG'`
.
[1]: http://lesscss.org/
[2]: http://nodejs.org/
[3]: https://code.google.com/p/wkhtmltopdf/
