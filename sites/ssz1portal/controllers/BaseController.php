<?php

namespace ssz1portal\controllers;

use app\core\controllers\Controller;

class BaseController extends Controller
{
    public function __construct( array $configs = [] )
    {
        parent::__construct( $configs );
        $this->view->addBreadcrumb( 'home', 'Home', $this->url->to( 'home' ) );
    }
}
