<?php
/**
 *  Plugin
 *  為了讓系統架構更容易 插入 非主要功能的程式碼
 *  使用該方式來於處理副程式
 *
 */
class PluginManager
{

    /**
     *  event path
     */
    protected static $_path = null;

    /**
     *  event list, class names
     */
    protected static $_event = array();

    /**
     *  外掛啟用狀況 設定檔
     */
    protected static $_settingFile = null;

    /**
     *  init
     *  取得路徑, 登記裡面的 class, 等待其它程式的呼叫
     *
     *  @param $path
     */
    public static function init( $path, $settingFile=null )
    {
        self::$_path        = $path;
        self::$_settingFile = $settingFile;

        // parser event path
        foreach (glob($path."/*.php") as $filename ) {
            $fileInfo  = pathinfo($filename);
            $className = $fileInfo['filename'];
            $file      = self::$_path .'/'. $className . '.php';

            if ( !file_exists($file) ) {
                continue;
            }
            include_once($file);
            $class = new $className();
            $class->init();

            self::$_event[] = array(
                'name'   => $className,
                'desc'   => $class->getDesc(),
                'enable' => $class->isEnable(),
            );
        }

        // 如果有設定檔, plugin 啟動與否將 完全 由該設定來做
        // 所以如果 plugin 中有設定 enable=true , 將會無視
        if ( $settingFile && file_exists($settingFile) ) {
            $settingFileContent = file_get_contents($settingFile);
            $setting = json_decode($settingFileContent, true);
            self::settingProcess( $setting );
        }

    }

    public static function getSettingFile()
    {
        return self::$_settingFile;
    }

    public static function getList()
    {
        return self::$_event;
    }

    /**
     *  呼叫已訂閱的 plugin 程式
     *
     *  @param string  $method - program name
     *  @param array   $params - program params
     */
    public static function notify( $method, $params )
    {
        $method = trim($method);
        foreach ( self::$_event as $event ) {

            $className = $event['name'];
            $handler = array( $className, $method );
            if ( !is_callable($handler) ) {
                continue;
            }
            // forward_static_call_array( $handler , $params );

            // 如果不啟用, 就不需要 new class name
            if ( !$event['enable'] ) {
                continue;
            }

            $class = new $className();
            $class->$method( $params );
        }
    }

    /* --------------------------------------------------------------------------------
        private
    -------------------------------------------------------------------------------- */

    /**
     *  這裡將完全重新設定 enable 狀態為 false
     *  完全依照 $setting 來決定 enable 狀態
     *  @return array
     */
    private static function settingProcess( $setting )
    {
        // all enable = false
        $count = count(self::$_event);
        for ( $i=0 ; $i<$count; $i++ ) {
            self::$_event[$i]['enable'] = false;
        }

        foreach ( self::$_event as $index => $plugin ) {
            foreach ( $setting as $status ) {
                if ( $plugin['name'] === trim(key($status)) && current($status) == 1 ) {

                    self::$_event[$index]['enable'] = true;
                    break;
                }
            }
        }
    }

}
