<?php

namespace app\components;

use app\components\Config;

class Route
{
    protected static $instance;

    // components
    protected $config;
    protected $view;
    protected $isAjax;

    // project specific properties
    protected $siteId;
    protected $siteDir;

    /**
     * @var array $_listUri List of URI's to match against
     */
    private $_listUri = [];

    /**
     * @var array $_listCall List of closures to call
     */
    private $_listCall = [];

    /**
     * @var string $_trim Used class-wide items to clean strings
     */
    private $_trim = '/\^$';

    /**
     * @var string regex to find {id} or {slug}
     */
    private $_varRegex = '/\{\w+\}/';

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
        $this->view = View::getInstance();

        // project specific properties
        $this->siteId = $configs[ 'siteId' ] ?? getenv( 'SITE_ID' );
        $this->siteDir = $configs[ 'siteDir' ] ?? getenv( 'SITE_DIR' );

        $this->isAjax = !empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) === 'xmlhttprequest';
    }

    /**
     * add - Adds a URI and Function to the two lists
     *
     * @param string $uri A path such as about/system
     * @param object $function An anonymous function
     */
    public function add( $uri, $function )
    {
        $uri = trim( $uri, $this->_trim );

        // replace function whenever found same $uri
        if ( in_array( $uri, $this->_listUri ) ) {
            $key = array_search( $uri, $this->_listUri );
            $this->_listCall[ $key ] = $function;
        } else {
            $this->_listUri[] = $uri;
            $this->_listCall[] = $function;
        }
    }

    /**
     * listen
     * @desc Looks for a match for the URI and runs the related function
     */
    public function listen()
    {
        $parseUri = isset( $_SERVER[ 'REQUEST_URI' ] ) ? parse_url( $_SERVER[ 'REQUEST_URI' ] ) : '/';

        // @see parse_str() http://php.net/manual/en/function.parse-str.php
        $query = $parseUri[ 'query' ] ?? '';

        // Know Issue: When POST request is made without token, route will be redirected to /
        // @see app/components/Session.php:DEV_TOKEN
        // @see app/Bootstrap.php:Session::DEV_TOKEN
        $uri = $parseUri[ 'path' ];

        // remove query strings from uri
        $uri = preg_replace( '/\?.*/', '', $uri );

        $uri = trim( $uri, $this->_trim );
        $replacementValues = [];

        // List through the stored URI's
        foreach ( $this->_listUri as $listKey => $listUri ) {

            // @see http://php.net/manual/en/function.preg-replace.php
            $__listUri = preg_replace( $this->_varRegex, '.+', $listUri );

            // See if there is a match
            // @see http://php.net/manual/en/function.preg-match.php
            if ( preg_match( "#^$__listUri$#", $uri ) ) {

                // Replace the values
                $realUri = explode( '/', $uri );
                $fakeUri = explode( '/', $__listUri );

                // Gather the .+ values with the real values in the URI
                foreach ( $fakeUri as $key => $value ) {
                    if ( $value === '.+' ) {
                        $replacementValues[] = $realUri[ $key ];
                    }
                }

                try {
                    // Pass an array for arguments
                    return call_user_func_array( [ new $this->_listCall[ $listKey ][0], $this->_listCall[ $listKey ][1] ], $replacementValues );
                } catch ( \Exception $e ) {

                    if ( $this->isAjax ) {
                        echo $this->view->renderAsJson( [
                            'message' => $e->getMessage(),
                        ], 500 );
                        die();
                    }

                    $this->view->addParams( [
                        'errorTitle' => $e->getMessage(),
                        'errorMessage' => $e->getMessage()
                    ] );

                    echo $this->view->render( 'error' );
                    die();

                } catch ( \Error $e ) {

                    if ( $this->isAjax ) {
                        echo $this->view->renderAsJson( [
                            'message' => $e->getMessage(),
                        ], 500 );
                        die();
                    }

                    $this->view->addParams( [
                        'errorTitle' => $e->getMessage(),
                        'errorMessage' => $e->getMessage()
                    ] );

                    echo $this->view->render( 'error' );
                    die();
                }
            }
        }

        $this->view->addParams( [
            'errorTitle' => 404,
            'errorMessage' => 'Page not found.'
        ] );

        echo $this->view->render( 'error' );
        die();
    }
}
