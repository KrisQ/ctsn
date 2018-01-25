<?php

//  IF YOU ARE USING A LOCAL DEV ENIRONMENT EDIT
//    C:/Windows/System32/drivers/etc/hosts
//    - Add the dev url to the bottom.
//    - EX: 127.0.0.1       test.nombers.dev
//    conf/extra/httpd-vhosts.conf
//    - Add the Virual Host
//    Ex:
//    <VirtualHost *:80>
//         ServerName test.nombers.dev
//         DocumentRoot "C:/xampp/htdocs/nomers"
//        <Directory "C:/xampp/htdocs/nomers">
//             Options Indexes FollowSymLinks
//            AllowOverride All
//            Order allow,deny
//            Allow from all
//        </Directory>
//    </VirtualHost>

/**
 * Configuration
 *
 * For more info about constants please @see http://php.net/manual/en/function.define.php
 */

/**
 * Configuration for: URL
 * Here we auto-detect your applications URL and the potential sub-folder. Works perfectly on most servers and in local
 * development environments (like WAMP, MAMP, etc.). Don't touch this unless you know what you do.
 *
 * URL_PUBLIC_FOLDER:
 * The folder that is visible to public, users will only have access to that folder so nobody can have a look into
 * "/application" or other folder inside your application or call any other .php file than index.php inside "/public".
 *
 * URL_PROTOCOL:
 * The protocol. Don't change unless you know exactly what you do.
 *
 * URL_DOMAIN:
 * The domain. Don't change unless you know exactly what you do.
 *
 * URL_SUB_FOLDER:
 * The sub-folder. Leave it like it is, even if you don't use a sub-folder (then this will be just "/").
 *
 * URL:
 * The final, auto-detected URL (build via the segments above). If you don't want to use auto-detection,
 * then replace this line with full URL (and sub-folder) and a trailing slash.
 */
 if(php_sapi_name() != 'cli'){
  define('URL_PUBLIC_FOLDER', 'public');
  define('URL_PROTOCOL', 'http://');
  define('URL_DOMAIN', $_SERVER['HTTP_HOST']);
  define('URL_SUB_FOLDER', str_replace(URL_PUBLIC_FOLDER, '', dirname($_SERVER['SCRIPT_NAME'])));
  define('URL', URL_PROTOCOL . URL_DOMAIN . URL_SUB_FOLDER);
  define('APP_ROOT', URL_PROTOCOL . URL_DOMAIN . URL_SUB_FOLDER);
  define('IMG_ROOT', "TODO");
  define('DB_FILELIB', "TODO");
  define('HTTPS_APP_ROOT', APP_ROOT);
  define('ENVIRONMENT', 'development');
  // define('HTTPS_APP_ROOT', APP_ROOT);
}
else {
  define('URL_DOMAIN', 'cli');
}

/**
 * Configuration for: Database
 * This is the place where you define your database credentials, database type etc.
*/

ini_set("display_errors", 1);
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define("DB_NAME", "ctsn");
define("DB_USER", "root");
define("DB_PASS", "root");
define('DB_CHARSET', 'utf8');
define("HASH_COST_FACTOR", "10");
