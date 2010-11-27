<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Load db password of prod env from external file.
// This is a public SVN, I don't trust you :)
define('DB_PASS', APPLICATION_ENV==='production' ?
    require(realpath(dirname(__FILE__) . '/../data/private/dbpass.php'))
    : ''
);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    'D:/wamp/www/ZendFramework-1.10.7-minimal/library',
    '/home/elvisciotti/library/ZendFramework-1.10.8-minimal/library',
    get_include_path(),
)));

function pd()
{
    if (APPLICATION_ENV !== 'production') {
        echo '<pre>';
        foreach(func_get_args() as $arg) {
            print_r($arg);
        }
        debug_print_backtrace();
        die;
    }
}

function vd()
{
    if (APPLICATION_ENV !== 'production') {
        echo '<pre>';
        foreach(func_get_args() as $arg) {
            var_dump($arg);
        }
        debug_print_backtrace();
        die;
    }
}


/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();