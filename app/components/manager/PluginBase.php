<?php

/**
 *  
 */
class PluginBase
{
    /**
     *  說明
     */
    protected $desc = "沒有說明";

    /**
     *  是否啟用該外掛
     */
    protected $enable = false;

    /**
     *
     */
    public function __construct() {
        // 讀取 "安裝plugin" 資訊檔案
    }

    /**
     *  是否已安裝該 plugin
    public function isEnable()
    {
        // TODO: 請改成吃資訊檔
        return true;
    }
     */

    /**
     *  init
     */
    public function init()
    {
        // nothing
    }

    /**
     *  說明訊息
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     *  是否啟用該外掛
     */
    public function isEnable()
    {
        return $this->enable;
    }

}
