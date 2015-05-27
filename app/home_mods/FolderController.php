<?php

class FolderController extends ControllerBase
{

    public function indexAction( $folderKey )
    {
        $folders = new Folders();
        $folder = $folders->getFolder($folderKey);
        if ( !$folder ) {
            echo 'hello world';
            exit;
        }

        $this->view->setVars(array(
            'folder' => $folder,
        ));
    }


}