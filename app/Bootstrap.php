<?php

namespace app;

use app\components\{
    Auth,
    Route,
    Config,
    Session,
    Url,
    Cache
};
use app\core\{
    controllers\Controller,
    models\Model
};

// var_dump( getenv( 'SITE_DIR' ) ); die;
// ini_set( 'session.save_path', getenv( 'SITE_DIR' ) . '/sessions' );

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
    public $route;
    public $url;
    public $cache;
    public $session;
    public $auth;

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
        // components
        $this->config = Config::getInstance();
        $this->route = Route::getInstance();
        $this->url = Url::getInstance();
        $this->session = Session::getInstance();
        $this->auth = Auth::getInstance();

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

            if ( !$this->auth->isAuth() && empty( $_POST[ 'token' ] ) ) {
                http_response_code( 401 );
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

            // remove token reset, let it die after 5 min
            // @see app/components/Session.php:initToken()
            // $this->session->resetToken();

            // added `tok-ki-dev` for development work
            // @see @see app/components/Session.php:DEV_TOKEN
            $devToken = $token === Session::DEV_TOKEN && $this->config->get( 'app.envyronment', 'development' ) === 'development';
            $checkToken = $devToken ?: hash_equals( $sessionToken, $token );

            if ( !$this->auth->isAuth() && !$checkToken ) {

                http_response_code( 401 );
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
        // nothing for now
    }

    protected function setupRoute()
    {
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
    }
}
