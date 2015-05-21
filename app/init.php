<?php

//error_reporting(E_ALL ^ E_NOTICE);
error_reporting(-1);
ini_set('html_errors','Off');
ini_set('display_errors','Off');

if ( !extension_loaded('phalcon') ) {
    echo 'Framework Disabled';
    exit;
}

$configFile = realpath(__DIR__.'/config/config.php' ) or die('Please setting "config.php" file');
require_once($configFile);

// developer mode
if ('dev'===APP_ENVIRONMENT) {
    error_reporting(E_ALL);
    ini_set('html_errors','On');
    ini_set('display_errors','On');
}
/*
if ('127.0.0.1'===$_SERVER['REMOTE_ADDR']) {
    error_reporting(E_ALL);
    ini_set('html_errors','On');
    ini_set('display_errors','On');
}
*/

require_once('helper.php');


/**
 *  init
 */
$factoryApplication = function()
{
    $appPath = APP_BASE_PATH . '/app';

    // Register an autoloader
    $loader = new \Phalcon\Loader();
    $loader->registerDirs(array(
        $appPath .'/event/',
        $appPath .'/models/',
        $appPath .'/models/modelHelper/',
        $appPath .'/components/bridge/',
        $appPath .'/components/bridge/option/',
        $appPath .'/components/developer/',
        $appPath .'/components/helper/',
        $appPath .'/components/identity/',
        $appPath .'/components/manager/',
        $appPath .'/'. APP_PORTAL .'_mods/',
        $appPath .'/'. APP_PORTAL .'_mods/components/',
    ));
    $loader->registerClasses(array(
        'File_CSV_DataSource'   => $appPath .'/vendors/csv_parser/File_CSV_DataSource.php',
        'SqlFormatter'          => $appPath .'/vendors/csv_parser/SqlFormatter.php',
    ));
    $loader->registerNamespaces(array(
        'HipChat'               => $appPath .'/vendors/HipChat/',
        'Whoops'                => $appPath .'/vendors/whoops/',
        'Blocks'                => $appPath .'/'. APP_PORTAL .'_mods/blocks/',
    ));
    $loader->register();

    // start and get application
    $app = require( $appPath .'/'. APP_PORTAL . '_mods/setting/start.php' );

    // url() helper function
    RegisterManager::set('url', $app->url );

    //
    LogBrg::init(   APP_BASE_PATH .'/var/log'   );
    CacheBrg::init( APP_BASE_PATH .'/var/cache' );

    // custom
    $customLoader = function( $appPath, $di ) {
        require( $appPath .'/'. APP_PORTAL . '_mods/setting/custom.php' );
    };
    $customLoader($appPath, $app->getDi() );
    unset($customLoader);


    // event init
    Ydin\Event::init( APP_BASE_PATH . '/app/event' );

    // init footer
    Ydin\Event::notify('init_footer', array('app'=>$app) );


    return $app;
};


