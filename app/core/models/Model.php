<?php

namespace app\core\models;

use app\components\{
    Config,
    Cache,
    Url
};

abstract class Model
{
    protected static $instance;

    // project specific properties
    protected $siteId;
    protected $siteDir;

    // properties
    protected $_configs = [];

    // components
    protected $config;
    protected $cache;
    protected $url;

    public function __construct( array $configs = [] )
    {
        $this->cache = Cache::getInstance();
        $this->config = Config::getInstance();
        $this->url = Url::getInstance();

        // project specific properties
        $this->siteId = $configs[ 'siteId' ] ?? getenv( 'SITE_ID' );
        $this->siteDir = $configs[ 'siteDir' ] ?? getenv( 'SITE_DIR' );

        // set attributes
        $this->setAttributes( $configs );

        $this->_configs = $configs;
    }

    public function setAttributes( array $configs )
    {
        if ( $configs ) {
            foreach ( $configs as $property => $value ) {
                if ( property_exists( $this, $property ) ) {
                    $this->$property = $value;
                }
            }
        }
    }

    public function getConfigs()
    {
        return $this->_configs;
    }

    public function getProps()
    {
        $array = [];
        foreach ( get_object_vars( $this ) as $key => $value ) {
            if ( $key !== '_configs' ) {
                $array[ $key ] = $value;
            }
        }

        return $array;
    }
}
