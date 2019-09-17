<?php

namespace app\components;

use app\components\{Str};

class Cache
{
    protected static $instance;

    // properties
    private $m;

    // project specific properties
    protected $siteId;
    protected $siteDir;
    protected $siteEnv;

    public $config = [];

    public function getInstance( array $configs = [] )
    {
        if ( !isset( self::$instance ) ) {
            self::$instance = new self( $configs );
        }

        return self::$instance;
    }

    public function __construct( array $configs = [] )
    {
        // project specific properties
        $this->siteId = $configs[ 'siteId' ] ?? getenv( 'SITE_ID' );
        $this->siteDir = $configs[ 'siteDir' ] ?? getenv( 'SITE_DIR' ) ?? ( $this->siteId ? __DIR__ . '/../../sites/' . $this->siteId : '' );
        if ( !$this->siteDir ) {
            $this->siteDir = ( $this->siteId ? __DIR__ . '/../../sites/' . $this->siteId : '' );
        }
        $this->siteEnv = $configs[ 'siteEnv' ] ?? getenv( 'SITE_ENV' );
        $this->buildCacheConfig();

        $this->m = new \Memcached();
        $this->m->addServer( $this->getCacheConfig( 'host' ), $this->getCacheConfig( 'port' ) );
    }

    private function buildCacheConfig()
    {
        $this->config = require __DIR__ . '/../configs/memcached.php';

        if ( $this->siteId ) {
            $siteConfig = $this->siteDir . '/configs/memcached.php';
            if ( file_exists( $siteConfig ) ) {
                $this->config = array_replace_recursive( $this->config, require $siteConfig );
            }

            if ( $this->siteEnv ) {
                $envConfigFile = $this->siteDir . '/configs/' . $this->siteEnv . '.php';
                if ( file_exists( $envConfigFile ) ) {
                    $envConfig = require $envConfigFile;
                    if ( !empty( $envConfig[ 'memcached' ] ) ) {
                        $this->config = array_replace_recursive( $this->config, $envConfig[ 'memcached' ] );
                    }
                }
            }
        }

        return $this->config;
    }

    private function getCacheConfig( string $key = '' )
    {
        if ( $key ) {
            return data_get( $this->config, $key );
        }

        return $this->config;
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
}
