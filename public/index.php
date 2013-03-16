<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

/*
 * define APPLICATION_ENV as testing if we're on test.jimsmiley.us
 */
if( array_key_exists('SERVER_NAME', $_SERVER ) 
        && $_SERVER['SERVER_NAME'] == 'test.jimsmiley.us' ) {
    define( 'APPLICATION_ENV', 'testing' );
}

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

$application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();

$request_url = $_SERVER['REQUEST_URI'];

//forward to tphilly wordpress on an index request
if( preg_match(  '/^\/$/', $request_url ) 
        || preg_match(  '/\/index.php/', $request_url ) 
        ) {
    $application->getBootstrap()->setForwardLayout();
}

$application->run();