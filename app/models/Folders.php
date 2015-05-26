<?php

/**
 *
 */
class Folders extends ZendModel
{
    const CACHE_FOLDER = 'cache_folder';

    /**
     *  table master field key
     */
    protected $pk = 'key';

    /**
     *  table name
     */
    protected $tableName = 'folders';

    /**
     *  get method
     */
    protected $getMethod = 'getFolder';

    /**
     *  get db object by record
     *  @param  row
     *  @return TahScan object
     */
    public function mapRow( $row )
    {
        $object = new Folder();
        $object->setKey        ( $row['key']                     );
        $object->setReal       ( $row['real']                    );
        $object->setName       ( $row['name']                    );
        $object->setMtime      ( strtotime($row['mtime'])        );
        $object->setProperties ( unserialize($row['properties']) );
        return $object;
    }

    /* ================================================================================
        write database
    ================================================================================ */

    /**
     *  add Folder
     *  @param Folder object
     *  @return boolean
     */
    public function addFolder( $object )
    {
        $this->removeCache( $object );

        $result = $this->addObject( $object );
        if ( !$result ) {
            return false;
        }

        $object = $this->getFolder( $object->getKey() );
        if ( !$object ) {
            return false;
        }

        $this->preChangeHook( $object );
        return true;
    }

    /**
     *  rebuild Folder
     *  @param Folder object
     *  @return int
     */
    public function rebuildFolder( $object )
    {
        $this->removeCache( $object );

        $result = $this->deleteFolder( $object->getKey() );
        if ( !$result ) {
            return false;
        }
        
        return $this->addFolder( $object );
    }

    /**
     *  delete Folder
     *  @param string key
     *  @return boolean
     */
    public function deleteFolder( $key )
    {
        $object = $this->getFolder($key);
        if ( !$object ) {
            return false;
        }

        $this->removeCache( $object );

        if ( !$this->deleteObject($key) ) {
            return false;
        }
        return true;
    }

    /**
     *  pre change hook, first remove cache, second do something more
     *  about add, update, delete
     *  @param object
     */
    public function preChangeHook( $object )
    {
        // 重建 folder tags
        $this->resetFolderTags( $object );
    }

    /**
     *  remove cache
     *  @param object
     */
    protected function removeCache( $object )
    {
        if ( !$object->getKey() ) {
            return;
        }

        $cacheKey = $this->getFullCacheKey( $object->getKey(), Folders::CACHE_FOLDER );
        CacheBrg::remove( $cacheKey );
    }


    /* ================================================================================
        read access database
    ================================================================================ */

    /**
     *  get Folder by key
     *  @param  string key
     *  @return object or false
     */
    public function getFolder( $key )
    {
        return $this->getObject( 'key', $key, Folders::CACHE_FOLDER );
    }


    /* ================================================================================
        find Folders and get count
        多欄、針對性的搜尋, 主要在後台方便使用, 使用 and 搜尋方式
    ================================================================================ */

    /**
     *  find many Folder
     *  @param  option array
     *  @return objects or empty array
     */
    public function findFolders( $opt=array() )
    {
        $opt += array(
            'key'           => '',
            'real'          => '',
            'name'          => '',
            '_page'         => 1,
            '_itemsPerPage' => APP_ITEMS_PER_PAGE
        );
        return $this->findFoldersReal( $opt );
    }

    /**
     *  get count by "findFolders" method
     *  @return int
     */
    public function numFindFolders( $opt=array() )
    {
        $opt += array(
            'key'   => '',
            'real'  => '',
            'name'  => '',
        );
        return $this->findFoldersReal( $opt, true );
    }

    /**
     *  findFolders option
     *  @return objects or record total
     */
    protected function findFoldersReal( $opt=array(), $isGetCount=false )
    {
        $select = $this->getDbSelect();

        //
        if ( '' !== $opt['key'] ) {
            $select->where->and->equalTo( 'key', $opt['key'] );
        }
        if ( '' !== $opt['real'] ) {
            $select->where->and->equalTo( 'real', $opt['real'] );
        }
        if ( '' !== $opt['name'] ) {
            $select->where->and->equalTo( 'name', $opt['name'] );
        }

        if ( !$isGetCount ) {
            return $this->findObjects( $select, $opt );
        }
        return $this->numFindObjects( $select );
    }


    /* ================================================================================
        search , 前台搜尋使用
    ================================================================================ */

    /**
     *  search folders by tags
     *  @param  option array
     *  @return objects or empty array
     */
    public function SearchFolders( Array $opts )
    {
        $this->error = null;

        $sqlArray = array();
        foreach ( $opts as $opt ) {
            $sqlArray[] = "(`type`='{$opt['type']}' AND `val`='{$opt['value']}')";
        }

        $whereSql = join(" OR ", $sqlArray );

        $sql =<<<EOD
            SELECT  *
            FROM    `folder_tags`
            WHERE   {$whereSql}
EOD;

        try {
            $adapter   = $this->getAdapter();
            $statement = $adapter->query($sql);
            $results   = $statement->execute();
        }
        catch( Exception $e ) {
            $this->error = $e;
            return false;
        }

        if ( !$results ) {
            return array();
        }

        // 計算加權分數
        // 針對分數最高的 folder, 排在最前面
        $folderItems = array();
        while( $row = $results->next() ) {
            //pr($row);
            $key = $row['folder_key'];
            if ( !isset($folderItems[$key]) ) {
                $folderItems[$key] = array(
                    'key'         => $key,
                    'total_score' => 0,
                );
            }
            $folderItems[$key]['total_score'] += $row['score'];
        };
        // pr($folderItems);

        // 排序
        $customSort = function( $key )
        {
            return function ($a, $b) use ($key) {
                return strnatcmp($b[$key], $a[$key]);
            };
        };
        usort($folderItems, $customSort('total_score'));

        $objects = array();
        $getMethod = $this->getMethod;
        foreach ( $folderItems as $item ) {
            $objects[] = $this->$getMethod( $item['key'] );
        }
        return $objects;
    }

    /* ================================================================================
        folder_tags extends
    ================================================================================ */

    /**
     *  重建 folder tags
     */
    protected function resetFolderTags( $folder )
    {
        $table     = 'folder_tags';
        $mainField = 'folder_key';
        $key = $folder->getKey();

        // delete sql
        $select = $this->getDbSelectTable( $table );
        $select->where->equalTo( $mainField, $key );
        $result = $this->query( $select );
        if ( $result ) {
            while( $row = $result->next() ) {
                $delete = new Zend\Db\Sql\Delete($table);
                $delete->where(array(
                     $mainField => $key,
                ));
                $this->execute($delete);
            }
        }

        // insert sql
        $tags = $folder->getProperty('tags');
        if ( $tags ) {
            foreach ( $tags as $tag ) {
                $insert = new Zend\Db\Sql\Insert($table);
                $insert->values(array(
                     $mainField => $key,
                     'type'     => $tag['key'],
                     'val'      => $tag['value'],
                     'score'    => $tag['score'],
                ));
                $this->execute($insert);
            }
        }

    }
    
    

}
