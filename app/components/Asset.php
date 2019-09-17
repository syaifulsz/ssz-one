<?php

namespace app\components;

use app\components\Url;
use Illuminate\Support\Str;

class Asset
{
    protected static $instance;

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
        // project specific properties
        $this->siteId = $configs[ 'siteId' ] ?? getenv( 'SITE_ID' );
        $this->siteDir = $configs[ 'siteDir' ] ?? getenv( 'SITE_DIR' );
    }

    public function cacheBooster( string $uri, string $path = '', bool $base_uri = false ) : string
    {
        $instance = self::getInstance();
        $file = realpath( $path ?: ( $instance->siteDir . '/public/' . $uri ) );

        if ( !file_exists( $file ) ) {
            throw new \Error( __METHOD__ . ' ::: asset file not found ' . ( $path ?: ( $instance->siteDir . '/public/' . $uri ) ) );
        }

        $v = filemtime( $file );
        $__uri = $uri . '?v=' . $v;

        return $base_uri ? Url::getInstance()->base( $__uri ) : $__uri;
    }
}
