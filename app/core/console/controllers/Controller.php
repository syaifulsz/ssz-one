<?php

namespace app\core\console\controllers;

ini_set( 'display_errors', 1 );

use app\components\{
    Url,
    Cache,
    Config,
    Auth,
    Database,
    Session
};

abstract class Controller
{
    protected static $instance;

    // project specific properties
    protected $siteId;
    protected $siteDir;

    // properties
    protected $cache;
    protected $config;
    protected $url;
    protected $database;
    protected $session;
    protected $auth;

    public $profilerBegin = 0;
    public $profilerEnd = 0;
    public $profilerDuration = 0;

    public function getInstance( array $configs = [] )
    {
        if ( !isset( self::$instance ) ) {
            self::$instance = new self( $configs );
        }

        return self::$instance;
    }

    public function __construct( array $config = [] )
    {
        $this->profilerBegin = microtime( true );

        // components
        $this->config = Config::getInstance();
        $this->cache = Cache::getInstance();
        $this->url = Url::getInstance();
        $this->database = Database::capsule();
        $this->session = Session::getInstance();
        $this->auth = Auth::getInstance();

        $this->auth->refreshIdentity();

        // project specific properties
        $this->siteId = $configs[ 'siteId' ] ?? getenv( 'SITE_ID' );
        $this->siteDir = $configs[ 'siteDir' ] ?? getenv( 'SITE_DIR' );

        if ( $config ) {
            foreach ( $config as $property => $value ) {
                if ( property_exists( $this, $property ) ) {
                    $this->$property = $value;
                }
            }
        }
    }

    public function getProfilerDuration() : array
    {
        $this->profilerEnd = microtime( true );
        $this->profilerDuration = $this->profilerEnd - $this->profilerBegin;

        $hours = (int)( $this->profilerDuration / 60 / 60 );
        $minutes = (int)( $this->profilerDuration / 60 ) - $hours * 60;
        $seconds = (int)$this->profilerDuration - $hours * 60 * 60 - $minutes * 60;

        return [
            'h' => $hours,
            'm' => $minutes,
            's' => $seconds,
        ];
    }
}
