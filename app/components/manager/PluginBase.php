<?php

/**
 *  
 */
class PluginBase
{

    /**
     *  說明
     */
    private $desc = "沒有說明";

    /**
     *  外掛呼叫順序
     */
    private $order = 1000;

    /**
     *
     */
    public function __construct() {
        // 讀取 "安裝plugin" 資訊檔案
    }

    /**
     *  是否已安裝該 plugin
     */
    public function isEnable()
    {
        // TODO: 請改成吃資訊檔
        return true;
    }

}
