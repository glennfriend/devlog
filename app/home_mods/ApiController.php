<?php

class ApiController extends ControllerBase
{

    /**
     *  設定一組 外掛狀態 為 1 or 0
     */
    public function pluginAction( $key=null, $enable=null )
    {
        $settingFile = PluginManager::getSettingFile();
        if ( !$settingFile ) {
            // 如果沒有設定檔, 那麼該 API 則無作用
            echo '{"result":false, "message":"fail"}';
            exit;
        }

        $status = false;
        if ( $enable=='yes' ) {
            $status = true;
        }

        // $result -> 將現在所有的外掛狀態 + 即將要改變的該外掛狀態 都寫入檔案中
        $results = array();

        $plugins = PluginManager::getList();
        foreach ( $plugins as $plugin ) {

            if ( $plugin['name'] != trim($key) ) {
                $results[] = array(
                    $plugin['name'] => $plugin['enable']
                );
            }
            else {
                $results[] = array(
                    $plugin['name'] => $status
                );
            }

        }

        $content = json_encode($results);
        file_put_contents( $settingFile, $content );

        echo '{"result":true, "message":"success"}';
        exit;
    }

}