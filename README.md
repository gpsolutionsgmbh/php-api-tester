API Tester PHP Demo App
=============================================================

The demo console PHP application for automation of testing REST JSON APIs.

The application based on Symphony Console Component and provides multi-threading operations for parallel testing of multiple API's

Used modules and technologies
-----------------------------

 * [Pthreads - PHP extension](https://github.com/krakjoe/pthreads)
 * [Guzzle - PHP HTTP client](http://docs.guzzlephp.org)
 * [Monolog - Logging for PHP](https://github.com/Seldaek/monolog)
 * [Swiftmailer - Free Feature-rich PHP Mailer](http://swiftmailer.org)
 * [Symfony Console Component](http://symfony.com/doc/current/components/console.html)
 * [Symfony Cache Component](http://symfony.com/doc/current/components/cache.html)
 * [Symfony Yaml Component](http://symfony.com/doc/current/components/yaml.html)
 * [Symfony DependencyInjection Component](http://symfony.com/doc/current/components/dependency_injection.html)

Configuration
-------------
Run `composer install`

Use `app/config/config.yml` for application setup

Use `app/config/api-tester.json` or create other config for setup API tests

Notes how to setup tests you can find in `app/config/api-tester.schema.json`

Run
---

For run use this commands `php bin/console`

When you want to run many test cases for different APIs you can install pthreads extension and tests was run in multi-threading mode. Example of execution command:
`php -dextension=php_pthreads.dll bin/console`


