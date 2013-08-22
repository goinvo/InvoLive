InvoLive
========
Personal and Studio Data Collection Framework

![screen](https://raw.github.com/goinvo/InvoLive/develop/docs/img/screenshot.png)

##Requirements
*	PHP enabled webserver
*	Database system (Sqlite, MySql...)
*	OAuth PHP extension
*	Composer (PHP dependency manager)
*	SCSS compiler

##Setup
*	Clone this repository to your local machine.
*	Launch `composer install` within the directory.
*	Set read/writes permissions to `app/storage` by issuing `chmod -R 777 app/storage` from the project root.

####Setting up a database
*	Configure database settings in `app/config/database.php`.
*	Create required tables with `php artisan migrate`

####Installing OAuth PHP extension
To install this extension it is sufficient to launch `pecl install oauth`.

##API

The currently available API is documented at [http://docs.involive1.apiary.io/](http://docs.involive1.apiary.io/)

