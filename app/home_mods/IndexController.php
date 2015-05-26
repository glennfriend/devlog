<?php

class IndexController extends ControllerBase
{

    public function indexAction( $page=1 )
    {
        $myFolders = array();


        $searchData = $this->parseSearchWords( InputBrg::get('q') );
        if ( $searchData ) {
            //MonitorManager::on();
            $folders   = new Folders();
            $myFolders = $folders->searchFolders( $searchData );
            //MonitorManager::off();
        }

        $this->view->setVars(array(
            'searchData' => $this->makeSearchWord( $searchData ),
            'myFolders'  => $myFolders,
        ));
    }

    /**
     *  search word to search array
     */
    private function parseSearchWords( $searchWord )
    {
        $words = array();
        $searchWords = explode(" ", strtolower($searchWord) );
        foreach ( $searchWords as $word ) {

            $group = explode(":", $word );
            if ( 1==count($group) ) {
                $type  = 'tag';
                $value = trim($group[0]);
            }
            elseif ( 2==count($group) ) {
                $type  = trim($group[0]);
                $value = trim($group[1]);
            }
            else {
                continue;
            }

            // filter
            if ( !$type ) {
                continue;
            }
            if ( !$value ) {
                continue;
            }

            // save
            $words[] = array(
                'type'  => $type,
                'value' => $value,
            );

        }
        return $words;
    }

    /**
     *  search array to search word
     */
    private function makeSearchWord( $searchData )
    {
        $word = '';
        foreach ( $searchData as $data ) {
            if ( 'tag' == $data['type'] ) {
                $word .= $data['value'] . ' ';
            }
            else {
                $word .= $data['type'] . ':' . $data['value'] . ' ';
            }
        }
        return trim($word);
    }

}