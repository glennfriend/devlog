<?php

/**
 *  路徑管理
 */
class UrlManager
{

    /**
     *  儲存基本路徑資訊
     */
    protected static $url = array();

    /**
     *
     */
    public static function init( $option )
    {
        self::$url = array(
            'baseUri' => $option['baseUri'],
        );
    }

    /**
     *  傳回網站基本目錄 uri
     */
    public static function baseUri( $pathFile='' )
    {
        if ( !$pathFile ) {
            return self::$url['baseUri'];
        }
        return self::$url['baseUri'] .'/'. $pathFile;
    }

    /**
     *  傳回 theme 基本目錄 uri
     */
    public static function themeUri( $pathFile='' )
    {
        if ( !$pathFile ) {
            return self::baseUri('/themes');
        }
        return self::baseUri('/themes'. $pathFile );
    }

    /* ================================================================================
        extends
    ================================================================================ */

    /**
     *  傳回 frontend javascript 的目錄 uri
     */
    public static function js( $jsPathFile='' )
    {
        return self::baseUri( '/js'. $jsPathFile );
    }

    /**
     *  傳回 frontend theme 的圖片目錄 uri
     */
    public static function themeImage( $imagePathFile='' , $isHtml=false )
    {
        $url = self::themeUri( '/default/images/'. $imagePathFile );
        if( $isHtml ) {
            return '<img src="'. $url .'" />';
        }
        return $url;
    }


    /* ================================================================================
        產生專案以外的網址
    ================================================================================ */

    // public static function getxxxxxx()




}
