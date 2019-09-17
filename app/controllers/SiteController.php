<?php

namespace app\controllers;

use app\core\controllers\Controller;
use app\components\View;

class SiteController extends Controller
{
    public function index()
    {
        $this->render( 'index' );
    }

    public function example()
    {
        $this->render( 'example' );
    }
}
