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

##System Architecture
Invo Live is based on a central system that allows storing and retrieving of measurement data. 

Worker components are in charge of retrieving data from different services (eg. Bodymedia, Fitbit, Withings, Dropbox, Staffplan...) and storing the data in the central database.

Visualization routines will make use of the provided API to retrieve data necessary to create the visualizations.

![sysarch](https://raw.github.com/goinvo/InvoLive/develop/docs/img/sysarch.png)

##Data structures
The base data structure is the 'measurement', a measurement is can come from different sources and describe a variety of aspects. Eg (Files created, steps, # of commits, calories burned).

Each is associated to:

* 1	`user` : User to which measurement is related (Juhan, Reshma, Roger...)
* 1 `timestamp`: Measurement timestamp
* 1 `value`: Measurement value (3, 22.00, 50...)
* 1 `eventtype`: Measurement type (Files created, steps...)
* 1 `source`: Measurement (Dropbox, Fitbit...)
* 1 or more `attributes`: Additional measurement properties (filenames, projects...)

Diagram of a measurement and its relations:
![screen](https://raw.github.com/goinvo/InvoLive/develop/docs/img/measurement.png)

##API

The currently available API is documented at [http://docs.involive1.apiary.io/](http://docs.involive1.apiary.io/)
