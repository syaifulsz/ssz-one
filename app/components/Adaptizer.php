<?php

namespace app\components;

class Adaptizer
{
    public static $instance;

    // vendor
    public $mobileDetect;

    // project specific properties
    protected $siteId;
    protected $siteDir;

    // properties
    public $isDesktop = true;
    public $isMobile = false;
    public $isTablet = false;

    public function getInstance( array $config = [] ) : self
    {
        if ( empty( self::$instance ) ) {
            self::$instance = new self( $config );
        }

        return self::$instance;
    }

    public function __construct( array $configs = [] )
    {
        // components
        $this->mobileDetect = new \Mobile_Detect;

        // project specific properties
        $this->siteId = $configs[ 'siteId' ] ?? getenv( 'SITE_ID' );
        $this->siteDir = $configs[ 'siteDir' ] ?? getenv( 'SITE_DIR' );

        switch ( true ) {
            case $this->mobileDetect->isMobile() :
                $this->setMode( 'mobile' );
                break;
            case $this->mobileDetect->isTablet() :
                $this->setMode( 'tablet' );
                break;
            default :
                $this->setMode( 'desktop' );
                break;
        }
    }

    public function setMode( string $mode )
    {
        switch ( $mode ) {
            case 'desktop' :
                $this->isDesktop = true;
                $this->isMobile = false;
                $this->isTablet = false;
                break;
            case 'mobile' :
                $this->isDesktop = false;
                $this->isMobile = true;
                $this->isTablet = false;
                break;
            case 'tablet' :
                $this->isDesktop = false;
                $this->isMobile = false;
                $this->isTablet = true;
                break;
        }
    }

    public function getMode()
    {
        switch ( true ) {
            case $this->isMobile :
                return 'mobile';
                break;
            case $this->isTablet :
                return 'tablet';
                break;
            default :
                return 'desktop';
                break;
        }
    }
}
