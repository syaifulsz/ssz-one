<?php

namespace app\components;

use app\components\{
    Session,
    Config,
    Cache
};
use app\models\User;

class Auth
{
    const CACHE_KEY_AUTH_IDENTITY = 'CACHE_KEY_AUTH_IDENTITY';
    protected static $instance;

    // components
    protected $cache;

    // properties
    public $user;

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
        $this->session = Session::getInstance();
        $this->cache = Cache::getInstance();

        // project specific properties
        $this->siteId = $configs[ 'siteId' ] ?? getenv( 'SITE_ID' );
        $this->siteDir = $configs[ 'siteDir' ] ?? getenv( 'SITE_DIR' );
    }

    public function refreshIdentity( $refresh = false )
    {
        if ( $user = $this->session->getCookie( User::AUTH_COOKIE_KEY ) ) {

            if ( $this->user ) {
                return $this->setIdentity( $this->user );
            }

            if ( $cUser = $this->cache->get( self::CACHE_KEY_AUTH_IDENTITY ) ) {
                return $this->setIdentity( $cUser );
            }

            $user = json_decode( $user, true );

            if ( $user[ 'id' ] ) {
                $query = User::where( 'id', $user[ 'id' ] )->first();
                if ( $query ) {
                    return $this->setIdentity( $query );
                }
            }

            if ( $user = Config::getInstance()->get( 'fakeUser.' . $user[ 'username' ] ) ) {
                return $this->setIdentity( new User( $user ) );
            }
        }

        return $this->user = null;
    }

    public function setIdentity( $user )
    {
        $this->cache->set( self::CACHE_KEY_AUTH_IDENTITY, $user );
        return $this->user = $user;
    }

    public function isAuth() : bool
    {
        return !empty( $this->session->getCookie( User::AUTH_COOKIE_KEY ) );
    }

    public function isAdmin() : bool
    {
        return ( $this->user->getRole( true ) === User::ROLE_ADMIN );
    }

    public function getUser()
    {
        if ( $this->user ) {
            return $this->user;
        }

        return null;
    }

    public function logout()
    {
        $this->cache->remove( self::CACHE_KEY_AUTH_IDENTITY );
        $this->user = null;
        $this->session->removeCookie( User::AUTH_COOKIE_KEY );
    }
}
