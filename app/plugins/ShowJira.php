<?php

/**
 *  
 */
class ShowJira extends PluginBase
{

    public function init()
    {
        $this->desc = "show Jira link";
    }

    public function folder_view_header( $data )
    {
        $tags = $data['folder']->getProperty('tags');
        foreach ( $tags as $tag ) {
            if ( 'jira' != $tag['key'] ) {
                continue;
            }
            $id  = $tag['value'];
            $url = $this->getUrl($id);
            $data['view']->content_header .= '<a href="'. $url .'" target="_blank">'. $id .'</a>';
            break;
        }
    }

    /**
     *  
     */
    private function getUrl( $id )
    {
        return "https://simplybridal.atlassian.net/browse/" . $id;
    }

}
