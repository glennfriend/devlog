<?php

/**
 *  顯示附件
 */
class ShowAccessories extends PluginBase
{

    private $desc = "顯示附件";

    public function folder_view_header( $data )
    {
        $files = array();

        $accessories = $data['folder']->getProperty('accessories');
        foreach ( $accessories as $accessory ) {
            $files[] = '<li>'. basename($accessory['file']) .'</li>';
        }

        $data['view']->content_footer .= "<hr>";
        $data['view']->content_footer .= "<label>附件: </label>";
        $data['view']->content_footer .= "<ul>". join('', $files) ."</ul>";
    }

}
