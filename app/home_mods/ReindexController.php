<?php

class ReindexController extends ControllerBase
{

    public $allFolders = array();

    CONST WAIT_SECOND = 30;

    public function indexAction()
    {
        $root = UrlManager::baseIndexPath();
        $this->getAllFolders($root);
        $information = $this->parseAllKeyFile();
        $this->cleanFolders();

        $this->view->setVars(array(
            'information' => $information,
        ));
    }

    /* --------------------------------------------------------------------------------
        private
    -------------------------------------------------------------------------------- */

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
                if ( 'devlog.txt'==basename($pathName) ) {
                    // 不會把 devlog.txt 加入降件清單
                    continue;
                }
                $files[] = $this->getAccessoryInfo($pathName);
            }
        }

        return $files;
    }

    /**
     *  最主要 解析 的主程式
     *  新增、更新 Folder 資訊
     */
    private function parseAllKeyFile()
    {
        $information = array(
            'num_folders'     => 0,
            'num_tags'        => 0,
            'num_accessories' => 0,
            'tags'            => array(),
            'devlogs'         => array(),
        );

        // 先清除所有的 folder_tags
        $folders = new Folders();
        $folders->cleanAllFolderTags();

        foreach ( $this->allFolders as $folderName ) {

            // save information to output
            $information['num_folders']++;

            $file = $folderName .'/'. APP_KEY_FILE;
            if ( !file_exists($file) ) {
                continue;
            }

            $devlogManager = new DevlogFileManager( $file );
            if ( !$devlogManager->isExist() ) {
                continue;
            }

            $devlog = $devlogManager->getContentAndConvertEncoding(APP_GUESS_ENCODING);
            $devlog = $this->devlogProcess( $devlog, $folderName );
            $folderInfo = $this->makeFolderInfo( $folderName, $devlog );

            // debug
            // pr($file);  pr($devlog);  pr($folderInfo);  exit;

            // 目前, 只要 reindex, 就一定會寫回 devlog.txt
            // 之後可以看看是否在未變動的情況下, 就不用再覆寫
            $devlogManager->save( $devlog );

            // 一律重建 to database
            $folder = $this->makeNewFolder( $folderInfo );
            $folders->rebuildFolder( $folder );

            // save information to output
            $information['devlogs'][]       = $file;
            $information['num_tags']        += count($folderInfo['_tags']);
            $information['num_accessories'] += count($folderInfo['_accessories']);
            foreach ( $folderInfo['_tags'] as $tag ) {
                $value = $tag['key'].':'.$tag['value'];
                $hash  = md5($value);
                if ( !isset($information['tags'][$hash]) ) {
                    $information['tags'][$hash] = array(
                        'value'  => $value,
                        'counts' => 0,
                    );
                }
                $information['tags'][$hash]['counts']++;
            }

        }

        // sort information tag order
        $customSort = function( $key ) {
            return function ($a, $b) use ($key) {
                return strnatcmp($b[$key], $a[$key]);
            };
        };
        usort( $information['tags'], $customSort('counts') );

        //
        return $information;
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
        $keywords = str_replace( array('.','~','&') ,' ' , $folderName );
        $devlog['tag']  = $keywords;
        return $devlog;
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
                if ( !$item ) {
                    continue;
                }
                $tags[] = array( 
                    'key'   => $key,
                    'value' => $item,
                    'score' => & $score,
                );
            }
        }
        if ( count($tags)>0 ) {
            $score = floor( 100 / count($tags) );
        }

        $accessories = $this->getFolderAccessories( $folderName );

        $attrib = stat($folderName);
        $file = array(
            'key'           => md5($folderName),
            'real'          => $folderName,
            'name'          => basename($folderName),
          //'mtime'         => $attrib['mtime'], // 不準確, 不使用
            'mtime'         => $this->getLastUpdate( $accessories ),
            '_tags'         => $tags,
            '_accessories'  => $accessories,
        );
        return $file;
    }

    /**
     *  取得目錄之下, 所有檔案當中, 最後、最新的修改日期
     *  以程式結構來看, 直接就是 devlog.txt 是最後修改的檔案
     *
     *  @return int
     */
    private function getLastUpdate( Array $accessories )
    {
        if ( !is_array($accessories) ) {
            return false;
        }

        $mtimes = array();
        foreach ( $accessories as $fileInfo ) {
            $mtimes[] = $fileInfo['mtime'];
        }
        return max($mtimes);
    }

    /**
     *  array to new folder object
     */
    private function makeNewFolder( Array $folderInfo )
    {
        $folder = new Folder();
        $folder->setKey      ( $folderInfo['key']                           );
        $folder->setReal     ( $folderInfo['real']                          );
        $folder->setName     ( $folderInfo['name']                          );
        $folder->setMtime    ( $folderInfo['mtime']                         );
        $folder->setProperty ( 'tags', $folderInfo['_tags']                 );
        $folder->setProperty ( 'accessories', $folderInfo['_accessories']   );
        return $folder;
    }

    /**
     *
     */
    private function devlogProcess( $devlog, $folderName )
    {
        // 如果沒有設定初始值, 程式將自動分析目錄名稱, 自動猜測所需的值
        if ( !isset($devlog['tag']) || !$devlog['tag'] ) {
            $devlog = $this->guessValues( $devlog, $folderName );
        }

        // filter tag
        $devlog = $this->filterAllTag($devlog);
        return $devlog;
    }

    /**
     *  整理各種 tags ( tag, git, gira 等... )
     */
    private function filterAllTag( $devlog )
    {
        foreach ( $devlog as $type => $tagString ) 
        {
            if (!preg_match('/^[a-z][a-z-]*$/', $type)) {
                continue;
            }
            if ( mb_strlen($type)>16 ) {
                continue;
            }

            if ( is_array($tagString) ) {
                continue;
            }
            if ( is_object($tagString) ) {
                continue;
            }

            $words = explode(' ', $tagString );
            $tags = array();

            foreach ( $words as $value ) {
                $value = str_replace( array('~','!','%','_','(',')','<','>',',','.','?') ,'' ,$value );
                $value = trim($value);
                if (!$value) {
                    continue;
                }
                if ( strlen($value)<3 ) {
                    continue;
                }
                $tags[] = strtolower($value);
            }

            $devlog[$type] = join(' ', array_unique($tags) );
        }
        return $devlog;
    }

    /**
     *  檢查在資料表所有的資料, 不存在的就刪除
     */
    private function cleanFolders()
    {
        $folders = new Folders();
        $allFolders = $folders->findFolders(array(
            '_page' => -1,
        ));
        foreach( $allFolders as $folder )
        {
            if( !file_exists( $folder->getReal() ) ) {
                $folders->deleteFolder( $folder->getKey() );
            }
        }
    }

}
