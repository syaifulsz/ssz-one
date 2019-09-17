<?php

namespace app\components;

use app\components\{
    Str
};

class Cache
{
    protected static $instance;

    // properties
    private $m;

    // project specific properties
    protected $siteId;
    protected $siteDir;

    public function getInstance( array $configs = [] )
    {
        if ( !isset( self::$instance ) ) {
            self::$instance = new self( $configs );
        }

        return self::$instance;
    }

    public function __construct( array $configs = [] )
    {
        $this->m = new \Memcached();
        $this->m->addServer( $this->getCacheConfig( 'host' ), $this->getCacheConfig( 'port' ) );

        // project specific properties
        $this->siteId = $configs[ 'siteId' ] ?? getenv( 'SITE_ID' );
        $this->siteDir = $configs[ 'siteDir' ] ?? getenv( 'SITE_DIR' );
    }

    private function getCacheConfig( string $key = '' )
    {
        $config = require __DIR__ . '/../configs/memcached.php';
        if ( $this->siteId ) {
            $siteConfig = $this->siteDir . '/configs/memcached.php';
            if ( file_exists( $siteConfig ) ) {
                $config = array_replace_recursive( $config, require $siteConfig );
            }
        }

        if ( $key ) {
            return data_get( $config, $key );
        }

        return $config;
    }

    public function set( $key, $cacheData, $expire = false )
    {
        return $this->m->set( $key, $cacheData, $expire );
    }

    public function get( $key )
    {
        return $this->m->get( $key );
    }

    public function remove( $key )
    {
        return $this->m->delete( $key );
    }

    public function removeAll()
    {
        return $this->m->flush();
    }

    public function createKey( $keys, $md5 = true )
    {
        if ( is_array( $keys ) ) {
            $keys = http_build_query( $keys );
        }

        if ( !$md5 ) {
            return Str::slugify( urldecode( $keys ), '-' );
        }

        return md5( $keys );
    }

    public function isRefreshCache()
    {
        return ( @$_GET['clearCache'] === 'refresh' );
    }

    public function isRefreshCacheConfig()
    {
        return ( @$_GET['clearCache'] === 'config' );
    }
}
