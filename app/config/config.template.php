<?php

    /**
     *  設置規定:
     *
     *      所有路徑最後面都不能包含 "/" 符號
     *
     */

    /**
     *  Environment
     *      dev
     *      live
     */
    // define('APP_ENVIRONMENT', 'live' );
    define('APP_ENVIRONMENT', 'dev' );

    /**
     *  每個網站剛建立時要設定的私有密碼
     *  用於資料無法變動情況下的資料加密 Salted Hash
     *  當有相關加密資料建立之後, 該值將不能再變更
     *
     *  example:
     *      mix user password in database
     *
     */
    define('APP_PRIVATE_STATIC_CODE', '' );

    /**
     *  網站可變動式的加密值
     *  運用於生命週期短, 並且不會儲存起來的情況
     *  修改的時機通常為停機當下
     *
     *  example:
     *      web service encode
     *      cache key encode
     *
     */
    define('APP_PRIVATE_DYNAMIC_CODE', '' );

    /**
     *  mySQL Adapter
     */
    define('APP_DB_MYSQL_HOST', 'localhost'     );
    define('APP_DB_MYSQL_USER', 'root'          );
    define('APP_DB_MYSQL_PASS', ''              );
    define('APP_DB_MYSQL_DB',   'devlog'        );

    /**
     *  default items per page
     */
    define('APP_ITEMS_PER_PAGE', 15 );

    /**
     *  login lifetime
     *      phalcon - 是在 執行時期 運作, 所以重新設定之後, 立即生效
     *      Yii     - 是在 設定時期 運作, 所以重新設定之後, 要先清除所有的 cache 才會生效
     *
     *  2 * 60 * 60 = 2H =  7200
     *  3 * 60 * 60 = 3H = 10800
     *
     */
    define('APP_LOGIN_LIFETIME', 10800 );

    /* ================================================================================
        Cache & Memcache
    ================================================================================ */

    /**
     *  cache key
     */
    define('APP_CACKE_KEY', APP_PRIVATE_DYNAMIC_CODE );

    /**
     *  cache lifetime
     *  Yii lifetime 是在設定時期運作, 所以重新設定之後, 要先清除所有的 cache 才會生效
     *
     *  16 * 60 * 60 = 16H = 57600
     *
     */
    define('APP_CACHE_LIFETIME', 57600 );

    /**
     *  'file' or 'memcache'
     */
    define('APP_CACHE_TYPE', 'file');
    //define('APP_CACHE_TYPE', 'memcache');

    /**
     *
     */
    define('APP_MEMCACHE_HOST', 'localhost' );
    define('APP_MEMCACHE_PORT', '11211' );


    /* ================================================================================
        path and uri
    ================================================================================ */
    /**
     *  project base path
     */
    define('APP_BASE_PATH', '/var/www/devlog' );

    /**
     *  home uri
     */
    define('APP_HOME_URI', '/devlog' );

    /* ================================================================================
        resource paths
    ================================================================================ */

    /**
     *  程式上線之後，一但修改該參數，則必須要將資料表清除，重新索引
     */
    define('APP_SCAN_PATH', '/home/public-developer' );

    /**
     *  程式上線之後，一但修改該參數，則必須要將資料表清除，重新索引
     */
    define('APP_RESOURCE_URI', 'file://training/public-developer' );

    /**
     *  parse config file
     */
    define('APP_KEY_FILE', 'devlog.txt' );

    /* ================================================================================
        
        
    ================================================================================ */

    /**
     *  編碼轉換
     *  請加入所有使用的編碼習慣, 來編輯這個內容
     *
     *  @see mb_detect_encoding  
     *  example:
     *
     *      'UTF-8, BIG-5'
     *
     */
    define('APP_GUESS_ENCODING', 'UTF-8, BIG-5' );



    /* ================================================================================
        php ini setting
    ================================================================================ */

    date_default_timezone_set('Asia/Tapiei');
    ini_set( 'date.timezone', 'Asia/Tapiei');


    // PHP 5.6 setting to php.ini
    if ( phpversion() > '5.6' ) {
        ini_set('default_charset', 'UTF-8');
    }


    /* ================================================================================
        quick debug
    ================================================================================ */

    /*
    if ('your-ip'===$_SERVER['REMOTE_ADDR']) {
        error_reporting(E_ALL);
        ini_set('html_errors','On');
        ini_set('display_errors','On');
    }
    */  



