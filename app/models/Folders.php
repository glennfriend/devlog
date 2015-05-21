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
        $object->setSize       ( $row['size']                    );
        $object->setMtime      ( strtotime($row['mtime'])        );
        $object->setTags       ( $row['tags']                    );
        $object->setProperties ( unserialize($row['properties']) );
        return $object;
    }

    /* ================================================================================
        write database
    ================================================================================ */

    /**
     *  add Folder
     *  @param Folder object
     *  @return insert id or false
     */
    public function addFolder( $object )
    {
        $result = $this->addObject( $object );
        if ( !$result ) {
            return false;
        }

        $object = $this->getFolder( $insertId );
        if ( !$object ) {
            return false;
        }

        $this->preChangeHook( $object );
        return true;
    }

    /**
     *  update Folder
     *  @param Folder object
     *  @return int
     */
    public function updateFolder( $object )
    {
        $result = $this->updateObject( $object );
        if ( !$result ) {
            return false;
        }

        $this->preChangeHook( $object );
        return $result;
    }

    /**
     *  delete Folder
     *  @param int id
     *  @return boolean
     */
    public function deleteFolder( $id )
    {
        $object = $this->getFolder($id);
        if ( !$object || !$this->deleteObject($id) ) {
            return false;
        }

        $this->preChangeHook( $object );
        return true;
    }

    /**
     *  pre change hook, first remove cache, second do something more
     *  about add, update, delete
     *  @param object
     */
    public function preChangeHook( $object )
    {
        // first, remove cache
        $this->removeCache( $object );

        // second do something
        // 因為自身修改的影響, 必須要修改其它資料表記錄的欄位值
        /*
            // 例如 add article comment , 則 article of num_comments field 要做更新
            $article = $object->getArticle();
            $article->setNumComments( $this->getNumArticleComments( $article->getId() ) );
            $articles = new Articles();
            $articles->updateArticle( $article );
        */
    }

    /**
     *  remove cache
     *  @param object
     */
    protected function removeCache( $object )
    {
        if ( $object->getId() <= 0 ) {
            return;
        }

        $cacheKey = $this->getFullCacheKey( $object->getId(), Folders::CACHE_FOLDER );
        CacheBrg::remove( $cacheKey );
    }


    /* ================================================================================
        read access database
    ================================================================================ */

    /**
     *  get Folder by id
     *  @param  int id
     *  @return object or false
     */
    public function getFolder( $key )
    {
        return $this->getObject( 'key', $key, Folders::CACHE_FOLDER );
    }


    /* ================================================================================
        
    ================================================================================ */


    /* ================================================================================
        extends
    ================================================================================ */

}

