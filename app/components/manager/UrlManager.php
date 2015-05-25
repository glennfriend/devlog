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
     *  傳回 theme 基本目錄 uri   TODO: please deiete it
     */
     /*
    public static function themeUri( $pathFile='' )
    {
        if( !self::$_isSetting ) {
            return '';
        }

        if ( !$pathFile ) {
            return self::$_data['themeUri'];
        }
        else {
            return self::$_data['themeUri'] . '/' . $pathFile;
        }
    }
*/

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

    /**
     *  傳回 frontend javascript 的目錄 uri
     */
    public static function js( $jsPathFile='' )
    {
        if( !self::$_isSetting ) {
            return;
        }

        return self::$_data['baseUri'] . '/js' . $jsPathFile ;
    }

    /**
     *  傳回 frontend image 的目錄 uri
     */
    public static function imageUri( $pathFile='' )
    {
        if( !self::$_isSetting ) {
            return '';
        }

        if ( !$pathFile ) {
            return self::$_data['baseUri'] . '/images';
        }
        else {
            return self::$_data['baseUri'] . '/images' . $pathFile;
        }
    }

    /* ================================================================================
        產生專案以外的網址
    ================================================================================ */

    // public static function getxxxxxx()




}
