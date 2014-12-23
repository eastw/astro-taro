<?php
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application/'));
define('APPLICATION_ENV', 'development');

/**
 * Setup for includes
 */
set_include_path(
    APPLICATION_PATH . '/../library' . PATH_SEPARATOR .
    APPLICATION_PATH . '/../application/models' . PATH_SEPARATOR .
    get_include_path());

/**
 * Zend Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';

$autoloader = Zend_Loader_Autoloader::getInstance();

/**
 * Register my Namespaces for the Autoloader
 */
$autoloader->registerNamespace('App_');


/**
 * Include my complete Bootstrap
 */
require '../application/Bootstrap.php';

// initialize Zend_Application
$application = new Zend_Application (
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();

/**
 * Setup the CLI Commands
 * ../application/daily-horoscopes.php --help
 * ../application/daily-horoscopes.php --refresh
 */
try {
    $opts = new Zend_Console_Getopt(
        array(
            'help'      => 'Displays usage information.',
            'refresh'   => 'Refresh daily horoscopes',
        )
    );
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    exit($e->getMessage() ."\n\n". $e->getUsageMessage());
}

if(isset($opts->help)) {
    echo $opts->getUsageMessage();
    exit;
}

/**
 * Action : refresh
 */
if(isset($opts->refresh)) {
    $service = new App_HoroscopeService();
    $service->cronDailyHoroscopesUpdate();
}

