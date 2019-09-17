<?php

namespace app;

use Illuminate\Support\Str;
use app\components\{
    Route,
    Config,
    Session,
    Database,
    Url,
    Auth,
    Cache
};

define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS',   60 * MINUTE_IN_SECONDS );
define( 'DAY_IN_SECONDS',    24 * HOUR_IN_SECONDS   );
define( 'WEEK_IN_SECONDS',    7 * DAY_IN_SECONDS    );
define( 'MONTH_IN_SECONDS',  30 * DAY_IN_SECONDS    );
define( 'YEAR_IN_SECONDS',  365 * DAY_IN_SECONDS    );

class Bootstrap
{
    protected static $instance;

    // components
    public $config;
    public $session;
    public $route;
    public $url;
    public $database;
    public $cache;
    public $auth;
    public $cli;

    // properties
    protected $errorLogFile;
    protected $dateTimeZone;

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

    public function getSiteDir()
    {
        return realpath( $this->siteDir );
    }

    public function __construct( array $configs = [] )
    {
        $this->cli = php_sapi_name() === 'cli';

        // components
        $this->config = Config::getInstance( $configs );
        $this->cache = Cache::getInstance( $configs );
        $this->session = Session::getInstance( $configs );
        $this->route = Route::getInstance( $configs );
        $this->url = Url::getInstance( $configs );
        $this->database = Database::getInstance( $configs );
        $this->auth = Auth::getInstance( $configs );

        // properties
        $this->errorLogFile = __DIR__ . '/runtime/logs/app.log';
        $this->dateTimeZone = $this->config->get( 'app.timezone' );

        // project specific properties
        $this->siteId = $configs[ 'siteId' ] ?? getenv( 'SITE_ID' );
        $this->siteDir = $configs[ 'siteDir' ] ?? getenv( 'SITE_DIR' );

        // set property configs
        if ( $configs ) {
            foreach ( $configs as $property => $value ) {
                if ( property_exists( $this, $property ) ) {
                    $this->$property = $value;
                }
            }
        }

        // inits
        $this->setupDateTimeZone();
        $this->setupErrorLog();
        $this->setupToken();
        $this->setupRoute();
        $this->setupDatabase();
    }

    protected function setupErrorLog()
    {
        ini_set( 'error_log', $this->errorLogFile );
        ini_set( 'xdebug.max_nesting_level', 400 );
    }

    protected function setupDateTimeZone()
    {
        date_default_timezone_set( $this->config->get( 'app.timezone' ) );
    }

    protected function setupToken()
    {
        session_start();

        $this->session->initToken();

        if ( !empty( $_POST ) ) {

            $doToken = $_POST[ '_token_ignore' ] ?? !$this->auth->isAuth();

            if ( $doToken && empty( $_POST[ 'token' ] ) ) {
                // http_response_code( 401 );
                $this->session->setMessage( [
                    'tag' => 'alert',
                    'type' => 'danger',
                    'message' => 'Session expired! Please try again.',
                    'data' => []
                ] );
                $this->url->redirect( $this->url->to( 'adminLogin' ) );
                exit;
            }

            $sessionToken = $_SESSION[ 'token' ];
            $token = @$_POST[ 'token' ];
            $this->session->resetToken();

            if ( $doToken && !hash_equals( $sessionToken, $token ) ) {

                // http_response_code( 401 );
                $this->session->setMessage( [
                    'tag' => 'alert',
                    'type' => 'danger',
                    'message' => 'Session expired! Please try again.',
                    'data' => []
                ] );
                $this->url->redirect( $this->url->to( 'adminLogin' ) );
                exit;
            }
        }
    }

    protected function setupDatabase()
    {
        if ( $this->config->get( 'database.useMysql' ) ) {
            $this->database->getInstance();
        }
    }

    protected function setupRoute()
    {
        if ( $this->cli ) {
            if ( !empty( $_SERVER[ 'argv' ][ 1 ] ) ) {
                $controller = Str::studly( $_SERVER[ 'argv' ][ 1 ] ) . 'Controller';

                $args = $_SERVER[ 'argv' ];
                unset( $args[ 0 ] );
                unset( $args[ 1 ] );

                $params = [];
                foreach ( $args as $__param ) {
                    if ( Str::contains( $__param, '=' ) ) {
                        $param = explode( '=', $__param );
                        $key = str_replace( '-', '', $param[ 0 ] );
                        $params[ $key ] = $param[ 1 ];
                    }
                }

                require $this->siteDir . '/console/controllers/' . $controller . '.php';
                return ( new $controller( $params ) )->run();
            } else {

                echo 'Available controllers:' . PHP_EOL;
                foreach ( glob( $this->siteDir . '/console/controllers/*.php' ) as $file ) {
                    $parsed = explode( '/', $file );
                    $name = str_replace( 'Controller.php', '', end( $parsed ) );
                    echo ' - ' . Str::kebab( $name ) . PHP_EOL;
                }
            }
            die( 'Stop Cli' );
        }


        $routeConfigs = [];
        $routeDir = __DIR__ . '/configs/routes.php';
        foreach ( glob( $routeDir ) as $file ) {
            $routeConfigs =  require( $file );
        }

        if ( $this->siteId ) {
            foreach ( glob( $this->siteDir . '/configs/routes.php' ) as $file ) {
                $routeConfigs = array_merge( $routeConfigs, require( $file ) );
            }
        }

        foreach( $routeConfigs as $rule ) {
            $this->route->add( $rule[ 0 ], $rule[ 1 ] );
        }

        $this->route->listen();
        $this->session->clearMessages();
    }
}
