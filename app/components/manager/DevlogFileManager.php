<?php

class DevlogFileManager
{
    /**
     *  檔案完整路徑
     */
    protected $file = null;
    
    /**
     *  檢查檔案是否存在, 只會檢查第一次
     */
    protected $fileExist = false;
    
    /**
     *  parse 之後的結果儲存
     */
    protected $content = array();

    /**
     *  主要分隔符號
     */
    protected $chopChar = ':';

    /**
     *
     */
    public function __construct( $file )
    {
        if ( !file_exists($file) ) {
            $this->fileExist = false;
            return false;
        }

        $this->fileExist = true;
        $this->file = $file;

    }

    /**
     *  檔案是否存在
     */
    public function isExist()
    {
        return $this->fileExist;
    }

    /**
     *  取得檔案資訊
     *  如果檔案內容不是 utf-8 , 會嘗試轉換為該格式
     *
     */
    public function getContentAndConvertEncoding( $convertFromEncoding='UTF-8, BIG-5' )
    {
        if (!$this->fileExist) {
            return false;
        }

        $content = file_get_contents( $this->file );

        // 如果內容編碼不正確, 會強制轉換成 utf-8
        $encoding = mb_detect_encoding( $content, $convertFromEncoding );
        if ( 'UTF-8' != $encoding ) {
            $content = mb_convert_encoding( $content, "UTF-8", $encoding );
        }

        $content = $this->parseContent( $content );
        $content['tag'] = $this->parseTags( $content['tag'] );
        return $content;
    }

    /**
     *  寫回資訊
     *
     *  檔案會強制轉換成 utf-8
     */
    public function save( array $data )
    {
        if (!$this->fileExist) {
            return false;
        }

        $content = '';
        foreach ( $data as $key => $value ) {
            if ( is_array($value) ) {
                $content .= "// {$key} is array\n";
                foreach ( $value as $item ) {
                    $content .= "//     -> {$item}\n";
                }
                
                continue;
            }
            if ( is_object($value) ) {
                $content .= "// {$key} is object\n";
                continue;
            }
            $content .= $key .': '. $value ."\n";
        }

        return file_put_contents( $this->file, $content );
    }


    // --------------------------------------------------------------------------------
    // private
    // --------------------------------------------------------------------------------

    /**
     *  取得預設值, 目的之一是為了排列 keyword 的順序
     */
    private function getDefaultValues()
    {
        return array(
            'topic' => null,
            'tag' => null,
        );
    }

    /**
     *  解析 檔案
     */
    private function parseContent( $content )
    {
        $data = $this->getDefaultValues();
        $lines = explode("\n", $content );
        foreach ( $lines as $line ) { 
        
            $items = explode( $this->chopChar, $line );
            if (count($items) <= 1) {
                continue;
            } 

            $name = trim(strtolower($items[0]));
            unset($items[0]);
            $value = trim(join( $this->chopChar, $items ));

            // validate keyword
            if (!preg_match('/^[a-z][a-z-]*$/', $name)) {
                continue;
            }

            $data[$name] = $value;
        }


        return $data;
    }

    /**
     *  解析 tags
     */
    private function parseTags( $tagString )
    {
        $items = explode(' ', $tagString );
        $tags = array();

        foreach ( $items as $item ) {
            $item = trim($item);
            if (!$item) {
                continue;
            }
            $tags[] = $item;
        }
        return join(' ', array_unique($tags) );
    }

}

