<?php

// 如果不想啟用該功能
// return;

/**
 *  顯示所有外掛的位置
 */
class ShowAllPluginLocation extends PluginBase
{

    private $desc = "顯示所有外掛的所在位置";

    /**
     *
     */
    public function folder_view_header( $data )
    {
        $myName = debug_backtrace()[0]['function'];
        $this->show( $myName );
    }

    /**
     *
     */
    private function show( $html )
    {
        echo '<span style="color:#000000; background-color:#ccffcc; ">{ '. $html .' }</span>'."\n";
    }

}
