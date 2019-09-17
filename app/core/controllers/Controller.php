<?php

namespace app\core\controllers;

use app\components\{Auth, Session, View, Url, Cache, Config};

abstract class Controller
{
    protected static $instance;

    // project specific properties
    protected $siteId;
    protected $siteDir;

    // properties
    protected $layout = 'main';
    protected $breadcrumb;
    protected $view;
    protected $cache;
    protected $config;
    protected $url;
    protected $session;
    protected $auth;

    public function __construct( array $config = [] )
    {
        // components
        $this->config = Config::getInstance();
        $this->cache = Cache::getInstance();
        $this->view = View::getInstance();
        $this->url = Url::getInstance();
        $this->session = Session::getInstance();
        $this->auth = Auth::getInstance();

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

        $this->view->setLayout( $this->layout );
        $this->view->setBreadcrumb( [
            'home' => [
                'label' => 'Home',
                'url' => $this->url->base()
            ]
        ] );
    }

    protected function addBreadcrumb( string $key, string $label, string $url )
    {
        $this->view->addBreadcrumb( $key, $label, $url );
    }

    protected function addPageTitle( string $title )
    {
        $this->view->addPageTitle( $title );
    }

    public function getToken()
    {
        return $this->session->getToken();
    }

    public function render( string $template, array $data = [], bool $partial = false )
    {
        $this->view->addParams( $data );
        if ( $partial ) {
            return $this->view->render( $template );
        }
        echo $this->view->render( $template );
    }
}
