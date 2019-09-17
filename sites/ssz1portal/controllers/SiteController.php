<?php

namespace ssz1portal\controllers;

class SiteController extends BaseController
{
    public function __construct( array $configs = [] )
    {
        parent::__construct( $configs );
    }

    public function index()
    {
        $this->render( 'home/index' );
    }
}
