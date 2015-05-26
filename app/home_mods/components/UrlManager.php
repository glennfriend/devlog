<?php

/**
 *  路徑管理
 */
class UrlManager
{

    /**
     *  使用路徑管理之前, 必須要先設定
     */
    protected static $_isSetting = false;

    /**
     *  儲存基本路徑資訊
     */
    protected static $_data = array();

    /**
     *
     */
    public static function init( $option )
    {
        if( self::$_isSetting ) {
            return;
        }

        self::$_data = array(
            'baseUri' => $option['baseUri'],
        );

        self::$_isSetting = true;
    }

    /**
     *  傳回網站基本目錄 uri
     */
    public static function baseUri( $pathFile='' )
    {
        if( !self::$_isSetting ) {
            return;
        }

        if ( !$pathFile ) {
            return self::$_data['baseUri'];
        }
        else {
            return self::$_data['baseUri'] . '/' . $pathFile;
        }
    }


    /**
     *
     */
    public static function baseIndexPath()
    {
        return APP_SCAN_PATH;
    }

    /**
     *
     */
    public static function baseResourceUri()
    {
        return APP_RESOURCE_URI;
    }

    /* ================================================================================
        extends
    ================================================================================ */



    /* ================================================================================
        產生專案以外的網址
    ================================================================================ */

    /**
     *  產生類似 "\\public\work\hello.jpg" 的網址
     */
    public static function getRemote( $folder )
    {
        $uri = str_replace( APP_SCAN_PATH, '' , $folder->getReal() );
        $uri = trim( $uri , '/' );
        $uri = str_replace( '/', '\\' , $uri );
        return APP_REMOTE_URI .'\\'. $uri . '\\';
    }




}
