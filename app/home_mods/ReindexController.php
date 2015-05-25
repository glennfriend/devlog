<?php

class ReindexController extends ControllerBase
{

    public $allFolders = array();

    CONST WAIT_SECOND = 30;

    public function indexAction()
    {
        // $this->view->setVars();

        $root = UrlManager::baseIndexPath();
        $this->getAllFolders($root);
        $this->parseAllKeyFile();

        //pr($this->allFolders);
        exit;

        //$this->cleanFolders();
    }


    /**
     *  掃描 並且 索引 實際存在的 目錄 與 檔案
     *
     *  @param $pathRule, access path , EX. '/home/vivian' => '/home/vivian/＊'
     */
    private function getAllFolders( $pathRule )
    {
        $pathRule = $pathRule . '/*';
        $pathNames = glob($pathRule, GLOB_ONLYDIR);

        foreach ( $pathNames as $pathName ) {
            set_time_limit( self::WAIT_SECOND );
            $this->allFolders[] = $pathName;
            $this->getAllFolders( $pathName );
        }
    }

    /**
     *  掃描一個目錄下所有的附件
     */
    private function getFolderAccessories( $pathRule )
    {
        $pathRule = $pathRule . '/*';
        $pathNames = glob($pathRule);
        $files = array();

        foreach ( $pathNames as $pathName ) {
            set_time_limit( self::WAIT_SECOND );
            if ( is_dir($pathName) ) {
                $tmp = $this->getFolderAccessories($pathName);
                foreach ( $tmp as $file ) {
                    $files[] = $file;
                }
            }
            else {
                $files[] = $this->getAccessoryInfo($pathName);
            }
        }
        return $files;
    }

    /**
     *  取得附件資訊
     */
    private function getAccessoryInfo( $file )
    {
        $attrib = stat($file);
        return array(
            'file'  => $file,
            'mtime' => $attrib['mtime'],
            'size'  => $attrib['size'],
        );
    }

    /**
     *  程式將自動分析目錄名稱, 自動猜測所需的值
     */
    private function guessValues( Array $devlog, $folderPath )
    {
        $folderName = basename($folderPath);

        $tags = array();
        $keywords = str_replace( array('.','~','&') ,' ' , $folderName );
        $keywords = explode(' ', $keywords );
        foreach ( $keywords as $keyword ) {
            if ( mb_strlen($keyword)<4 ) {
                continue;
            }
            $tags[] = strtolower(trim($keyword));
        }

        $devlog['topic'] = $folderName;
        $devlog['tag']  = join(' ', $tags );
        return $devlog;
    }

    /**
     *  最主要 解析 的主程式
     */
    private function parseAllKeyFile()
    {

        foreach ( $this->allFolders as $folderName ) {

            $file = $folderName .'/'. APP_KEY_FILE;
            if ( !file_exists($file) ) {
                continue;
            }

            $devlogManager = new DevlogFileManager( $file );
            if ( !$devlogManager->isExist() ) {
                continue;
            }

            $devlog = $devlogManager->getContentAndConvertEncoding(APP_GUESS_ENCODING);
            $devlog['_accessories'] = $this->getFolderAccessories( $folderName );

            $folderInfo = $this->makeFolderInfo( $folderName, $devlog );

            pr($file);
            pr($folderInfo);
            pr($devlog);

            // 如果沒有設定初始值, 程式將自動分析目錄名稱, 自動猜測所需的值
            if ( !isset($devlog['tag']) || !$devlog['tag'] ) {
                $devlog = $this->guessValues( $devlog, $folderName );
            }

            //$devlogManager->save( $devlog );

            exit;
        }


/*

            //
            $folder = new Folders();
            $folder = $folders->getFolder( $file['key'] );
            if ( !$folder ) {
                // add
                $folder = $this->makeNewFolder($file);
                $folders->addFolder( $folder );
            }
            elseif( $file['mtime'] > $folder->getMtime() ) {
                // update
                $folder = $this->makeUpdateFolder( $folder, $file );
                $folders->updateFolder( $folder );
            }
            else {
                // 不變動
            }


*/
    }

    /**
     *  整理出所需要的 folder structure
     */
    private function makeFolderInfo( $folderName, $devlog )
    {
        $tags = array();
        $score = 0;
        foreach ( $devlog as $key => $itemString ) {
            if (!preg_match('/^[a-z][a-z-]*$/', $key )) {
                continue;
            }
            $items = explode(' ', $itemString);
            foreach ( $items as $item ) {
                $tags[] = array( 
                    'key'   => $key,
                    'value' => $item,
                    'score' => & $score,
                );
            }
        }
        if ( count($tags)>1 ) {
            $score = floor( 100 / count($tags) );
        }

        $attrib = stat($folderName);
        $file = array(
            'key'         => md5($folderName),
            'real'        => $folderName,
            'name'        => basename($folderName),
            'size'        => $attrib['size'],
          //'mtime'       => $attrib['mtime'], // 不準確, 不使用
            'mtime'       => $this->getLastUpdate( $devlog ),
            'tags'        => $tags,
        );
        return $file;
    }

    /**
     *  取得目錄之下, 所有檔案當中, 最後、最新的修改日期
     *  以程式結構來看, 直接就是 devlog.txt 是最後修改的檔案
     *
     *  @return int or false
     */
    private function getLastUpdate( Array $devlog )
    {
        if ( !isset($devlog['_accessories']) ) {
            return false;
        }

        $mtimes = array();
        foreach ( $devlog['_accessories'] as $fileInfo ) {
            $mtimes[] = $fileInfo['mtime'];
        }
        return max($mtimes);
    }

}


