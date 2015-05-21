<?php

class SessionBrgMemcache
{
    /**
     *  session
     */
    private $memcache;

    /**
     *  session init
     */
    public function init( $di )
    {
        $memcache = new Phalcon\Session\Adapter\Memcache(array(
            'host'          => APP_MEMCACHE_HOST,
            'port'          => APP_MEMCACHE_PORT,
          //'lifetime'      => 8600,
            'prefix'        => 'memcache_sess_',
            'persistent'    => false
        ));
        $memcache->start();

        $di->set('session', $memcache);
        $this->memcache = $memcache;
    }

    /* --------------------------------------------------------------------------------
        access
    -------------------------------------------------------------------------------- */

    /**
     *  get
     */
    public function get( $key, $defaultValue )
    {
        $val = $this->memcache->get($key);
        if ( !$val && $defaultValue ) {
            return $defaultValue;
        }
        return $val;
    }

    /* --------------------------------------------------------------------------------
        write
    -------------------------------------------------------------------------------- */

    /**
     *  set
     */
    public function set( $key, $value )
    {
        return $this->memcache->save( $key, $value );
    }

    /**
     *  remove
     */
    public function remove( $key )
    {
        $this->memcache->delete( $key );
    }

    /**
     *  destroy all
     */
    public function destroy()
    {
        $this->memcache->destroy();
    }

}
