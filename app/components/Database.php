<?php

/**
 * @since TW tengokwayang.com
 */
namespace app\components;

// components
use app\components\Config;

// vendors
use Illuminate\{
    Container\Container,
    Database\Capsule\Manager as Capsule
};

// use Jenssegers\Mongodb\Connection as MongoDBConnection;

class Database
{
    protected static $instance;

    // components
    protected $config;

    // properties
    protected $capsule;

    public function getInstance( array $configs = [] )
    {
        if ( !isset( self::$instance ) ) {
            self::$instance = new self( $configs );
        }

        return self::$instance;
    }

    public function __construct( array $config = [] )
    {
        // components
        $this->config = Config::getInstance();

        $this->capsule = new Capsule;

        // extends capsule add mongo
        // $capsule->getDatabaseManager()->extend('mongodb', function($config) {
        //     return new MongoDBConnection($config);
        // });

        $config = array_replace_recursive( $this->config->get( 'database', [] ), $config );

        // if ( !isset( $config[ 'mysql' ] ) && !isset( $config[ 'mongodb' ] ) ) {
        //     throw new \Error('Database configuration is not set!');
        // }

        // setup mysql connection
        if ( isset( $config[ 'mysql' ] ) ) {
            $this->capsule->addConnection( array_merge( [
                'driver'    => 'mysql',
                'host'      => '127.0.0.1',
                'port'      => 3306,
                'database'  => '',
                'username'  => 'root',
                'password'  => 'root',
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
            ], $config[ 'mysql' ] ) );
        }

        // setup mongo connection
        // if (isset($config['mongodb']['connection_name'])) {
        //     $this->capsule->addConnection(array_merge([
        //         'driver'   => 'mongodb',
        //         'host'     => 'twmongo',
        //         'port'     => '27017',
        //         'database' => 'tengokwayang_db_mongo',
        //         'username' => '',
        //         'password' => '',
        //         'options'  => [
        //             'database' => 'admin' // sets the authentication database required by mongo 3
        //         ]
        //     ], $config['mongodb']), @$config['mysql'] ? $config['mongodb']['connection_name'] : 'default');
        // }

        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
    }

    public function capsule()
    {
        return self::getInstance()->capsule;
    }
}
