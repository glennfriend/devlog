<?php

/**
 *  
 */
class ShowReadme extends PluginBase
{

    public function init()
    {
        $this->desc = "如果資料夾中有類似 readme.txt 的檔案名稱, 就顯示該內容";
    }

    public function folder_view_header( $data )
    {
        $readFileList = array(
            'readme.txt',
            'read.me',
            'readme',
        );
        $cntent = '';

        $accessories = $data['folder']->getProperty('accessories');
        foreach ( $accessories as $accessory ) {
            if ( !in_array(basename($accessory['file']), $readFileList) ) {
                continue;
            }

            $data['view']->content .= $this->getText($accessory['file']);
            break;
        }
    }

    /**
     *  
     */
    private function getText( $file )
    {
        if ( !file_exists($file) ) {
            return basename($file) . ' 檔案不存在';
        }

        $info = stat($file);
        if ( !$info ) {
            return '讀取檔案發生錯誤';
        }

        $size = $info['size'];
        $output = '';
        $max = 1048576; // 1 Mb
        if ( $size > $max ) {
            $output = file_get_contents_size( $file );
            $output = convert_utf8( $output );
            $output = trim($output) . "\n============================== (只取得部份內容) ==============================";
        }
        else {
            $output = file_get_contents( $file );
            $output = $this->convert_utf8( $output );
        }
        
        $output = htmlspecialchars( $output );
        return '<label>' . basename($file) .'</label>' . "\n<pre>" . $output ."</pre>";
    }

    /**
     *  將文字內容轉換為 utf-8 編碼
     */
    private function convert_utf8( $text )
    {
        mb_detect_order(array(
            'UTF-8',
            'BIG-5',
            'ASCII',
        ));

        $textEncoding = mb_detect_encoding($text);
        if ( 'UTF-8' != $textEncoding ) {
            $text = mb_convert_encoding( $text, 'UTF-8', $textEncoding );
        }
        return $text;
    }

}
