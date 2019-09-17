<?php

namespace app\components;

use app\components\{
    Config
};

class Url
{
    protected static $instance;

    // components
    public $config;

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
        // components
        $this->config = Config::getInstance();

        // project specific properties
        $this->siteId = $configs[ 'siteId' ] ?? getenv( 'SITE_ID' );
        $this->siteDir = $configs[ 'siteDir' ] ?? getenv( 'SITE_DIR' );
    }

    public function base( string $url = '' ) : string
    {
        $url = ltrim( $url, '/' );
        return $this->config->get( 'app.baseUrl' ) . ( $url ? "/{$url}" : '' );
    }

    public function siteBase( string $url = '' ) : string
    {
        $url = ltrim( $url, '/' );
        return $this->config->get( 'app.siteBaseUrl' ) . ( $url ? "/{$url}" : '' );
    }

    public function baseTo( string $name, array $params = [] ) : string
    {
        return $this->base( $this->to( $name, $params ) );
    }

    public function to( string $name, array $params = [] ) : string
    {
        $query = [];
        if ( $url = $this->config->get( 'routes.' . $name ) ) {
            $url = ltrim( $url[ 0 ], '/' );
            if ( $params ) {

                foreach ( $params as $key => $value ) {
                    $__key = '{' . $key . '}';
                    if ( str_contains( $url, $__key ) ) {
                        $url = str_replace( $__key, $value, $url );
                    } else {
                        $query[ $key ] = $value;
                    }
                }
            }
            $url = rtrim( preg_replace( '/\{\w+\}/', '', $url ), '/' ) . ( $query ? '?' . http_build_query( $query ) : '' );
        }

        return $url ? "/{$url}" : '/';
    }

    public function redirect( $url, $permanent = false )
    {
        header('Location: ' . $url, true, $permanent ? 301 : 302);
        exit();
    }
}
