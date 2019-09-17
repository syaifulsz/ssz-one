<?php

namespace app\components;

use app\components\{
    Adaptizer,
    Cache
};

class Config
{
    protected static $instance;

    // properties
    public $configs = [];
    public $masterCacheKey;

    // components
    protected $adaptizer;
    protected $cache;

    // project specific properties
    protected $siteId;
    protected $siteDir;
    protected $siteEnv;

    public function getInstance( array $configs = [] )
    {
        if ( !isset( self::$instance ) ) {
            self::$instance = new self( $configs );
        }

        return self::$instance;
    }

    public function __construct( array $configs = [] )
    {
        $this->adaptizer = Adaptizer::getInstance();
        $this->cache = Cache::getInstance();

        // project specific properties
        $this->siteId = $configs[ 'siteId' ] ?? getenv( 'SITE_ID' );
        $this->siteDir = $configs[ 'siteDir' ] ?? getenv( 'SITE_DIR' );

        $env = $configs[ 'siteEnv' ] ?? getenv( 'SITE_ENV' );
        $this->siteEnv = strtolower( $env );

        $this->masterCacheKey = md5( http_build_query( [
            $this->siteId,
            $this->siteDir,
            $this->siteEnv,
            $this->adaptizer->getMode()
        ] ) );

        $this->configs = $this->buildConfig( [
            'siteId' => $this->siteId,
            'siteDir' => $this->siteDir,
            'siteEnv' => $this->siteEnv
        ] );
    }

    private function adaptiveConfig( string $path ) : array
    {
        $mode = !$this->adaptizer->isDesktop ? $this->adaptizer->getMode() : '';

        if ( !$this->adaptizer->isDesktop ) {
            $path = realpath( $path );
            $paths = explode( '/', $path );
            $lastKey = count( $paths ) - 1;
            $paths[ $lastKey ] = ( $mode ? $mode . '/' : '' ) . $paths[ $lastKey ];
            $file = implode( '/', $paths );
            if ( file_exists( $file ) ) {
                return require( $file );
            }
        }

        return [];
    }

    private function buildConfig( array $params = [] ) : array
    {
        if ( ( $config = $this->cache->get( $this->masterCacheKey ) ) && !Cache::isRefreshCacheConfig() ) {
            return $config;
        }

        $config = [];
        $configDir = __DIR__ . '/../configs/*.php';
        foreach ( glob( $configDir ) as $file ) {
            $configKey = pathinfo( $file, PATHINFO_FILENAME );
            if ( $configKey !== 'columns' ) {
                $config[ $configKey ] = array_replace_recursive( require( $file ), $this->adaptiveConfig( $file ) );
            }
        }

        if ( $this->siteEnv ) {
            $envConfig = [];
            $envConfigPath = __DIR__ . '/../configs/' . $this->siteEnv . '.php';
            if ( file_exists( $envConfigPath ) ) {
                $config = array_replace_recursive( $config, require( $envConfigPath ), $this->adaptiveConfig( $envConfigPath ) );
            }
        }

        $local = [];
        $localPath = __DIR__ . '/../configs/local.php';
        if ( file_exists( $localPath ) ) {
            $config = array_replace_recursive( $config, require( $localPath ), $this->adaptiveConfig( $localPath ) );
        }

        // project specific configs
        if ( $this->siteId ) {
            $projectDir = $this->siteDir . '/configs';

            $projectConfig = [];
            foreach (glob( $projectDir . '/*.php' ) as $file) {
                if ( pathinfo( $file, PATHINFO_FILENAME ) !== 'column' ) {
                    $projectConfig[ pathinfo( $file, PATHINFO_FILENAME ) ] = array_replace_recursive( require( $file ), $this->adaptiveConfig( $file ) );
                }
            }

            if ( $this->siteEnv ) {
                $envConfig = [];
                $envConfigPath = $projectDir . '/' . $this->siteEnv . '.php';
                if ( file_exists( $envConfigPath ) ) {
                    $projectConfig = array_replace_recursive( $projectConfig, require( $envConfigPath ), $this->adaptiveConfig( $envConfigPath ) );
                }
            }

            $local = [];
            $localConfigPath = $projectDir . '/local.php';
            if ( file_exists( $localConfigPath ) ) {
                $projectConfig = array_replace_recursive( $projectConfig, require( $localConfigPath ), $this->adaptiveConfig( $localConfigPath ) );
            }

            $config = array_replace_recursive( $config, $projectConfig );
        }

        $config = array_merge( $config, $params );
        $this->cache->set( $this->masterCacheKey, $config );
        return $config;
    }

    public function all()
    {
        if ( empty( $this->configs ) ) {
            return $this->configs = $this->buildConfig();
        }

        return $this->configs;
    }

    public function get( string $key = '', $default = null )
    {
        if ( $key ) {
            return data_get( $this->configs, $key ) ?: $default;
        }

        return $default;
    }
}
