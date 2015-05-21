<?php
/**
 *  phalcon Memcache Cache
 */
class CacheBrgMemcache
{

    /**
     *  myself
     */
    private $cache = array();

    /**
     *  init
     */
    public function init( $frontCache )
    {
        $this->cache = $cache = new Phalcon\Cache\Backend\Memcache($frontCache, array(
            "host" => APP_MEMCACHE_HOST,
            "port" => APP_MEMCACHE_PORT
        ));
    }

    /* --------------------------------------------------------------------------------
        access
    -------------------------------------------------------------------------------- */

    /**
     *  get cache
     */
    public function get( $key )
    {
        return $this->cache->get( $key );
    }


    /* --------------------------------------------------------------------------------
        write
    -------------------------------------------------------------------------------- */

    /**
     *  set cache
     */
    public function set( $key, $value )
    {
        $this->cache->save( $key, $value );
    }

    /**
     *  remove cache
     */
    public function remove( $key )
    {
        $this->cache->delete( $key );
    }

    /**
     *  clean all cache data
     */
    public function flush()
    {
        $keys = $this->cache->queryKeys();
        foreach ( $keys as $key ) {
            $this->cache->delete($key);
        }
    }

}
