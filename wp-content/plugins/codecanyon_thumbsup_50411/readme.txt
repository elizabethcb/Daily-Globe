ThumbsUp

Created: July 2009
By: Geert De Deckere <geert@idoe.be>

---------------------------------------------------------------------------------------------------


For the most readable and up-to-date installation instructions, please go to the online demo at:
http://www.geertdedeckere.be/lab/themeforest/thumbsup/demo/#install

This online demo has also been provided in the download. Have a look at the demo folder.


----- SERVER REQUIREMENTS

ThumbsUp requires PHP 5. It is coded using PHP 5 OOP.

You will also need SQLite, but that should not be a problem since the SQLite extension is enabled by default as of PHP 5. I tested it with SQLite 2.8.17.

You can retrieve all the information you need by running phpinfo() on your server.


----- STEP 1 — CONFIGURATE

Open config.php inside the thumbsup folder. This is your only configuration file. The settings are well documented. Be sure to update the following:

THUMBSUP_WEBROOT: the URL-base for your website. Must begin and end with a slash. This should match the location where you will upload the thumbsup folder in step 2.
For example: '/' or '/sub/directory/'

date_default_timezone_set(): default timezone used by all PHP date/time functions.
For example: Europe/London, America/New_York

admins: array containing one or more administrators accounts. Username is stored as array key, password as array value. You need to SHA1 encode the passwords.
For example: 'joe' => 'cba33ce31e463b5766dc97fccb8954f42dc2bb08'


----- STEP 2 — UPLOAD

Using your favorite FTP-client, upload the whole thumbsup folder to the root of your website. This location should match THUMBSUP_WEBROOT from config.php.

One subdirectory in the thumbsup folder is called database. This folder needs to be writable by the webserver (chmod 666). It is where the SQLite database will be stored.
Note: you can change the location of the database file via THUMBSUP_DATABASE in config.php.


----- STEP 3 — INCLUDE

At the top of your webpage (before any output) add the following line of PHP.
<?php include './thumbsup/core/thumbsup.php' ?>
Note: ./ is relative to the current directory. You may need to change the path to reflect your current position in the directory structure.

Load jQuery (>=1.3) and the ThumbsUp jQuery file in the <head> of your HTML.
<script type="text/javascript" src="http://yoursite.com/path/to/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo THUMBSUP_WEBROOT ?>thumbsup/core/thumbsup.js.php"></script>


----- STEP 4 — CREATE

You're almost done. At this point it is just a matter of creating ThumbsUp items anywhere you want on your webpage. More about this in the documentation.
<?php $thumbsup->setup('name', 'template')->render() ?>

See: http://www.geertdedeckere.be/lab/themeforest/thumbsup/demo/#documentation

---------------------------------------------------------------------------------------------------


Thank you for purchasing ThumbsUp!