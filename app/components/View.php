<?php

namespace app\components;

use app\components\{
    Config,
    Adaptizer
};

class View
{
    const BLOCK_HEAD = 'blockHead';
    const BLOCK_BODY_START = 'blockBodyStart';
    const BLOCK_BODY_END = 'blockBodyEnd';

    public static $instance;

    // components
    protected $config;
    protected $url;
    protected $adaptizer;

    // properties
    public $viewDir;
    public $appViewDir;
    public $headContent = [];
    public $footContent = [];
    public $layoutName = 'main';
    public $layout;
    public $params = [];
    public $bodyClass = [];
    public $pageTitle = [];
    public $pageDescription = [];
    public $breadcrumb = [];
    public $htmlClass = [];
    public $blockHead = [];
    public $blockBodyStart = [];
    public $blockBodyEnd = [];
    public $templates = [];

    // project specific properties
    protected $siteId;
    protected $siteDir;

    public function getInstance( array $config = [] ) : self
    {
        if ( empty( self::$instance ) ) {
            self::$instance = new self( $config );
        }

        return self::$instance;
    }

    public function __construct( array $config = [] )
    {
        // components
        $this->config = Config::getInstance();
        $this->url = Url::getInstance();
        $this->adaptizer = Adaptizer::getInstance();

        // project specific properties
        $this->siteId = $configs[ 'siteId' ] ?? getenv( 'SITE_ID' );
        $this->siteDir = $configs[ 'siteDir' ] ?? getenv( 'SITE_DIR' );
        $this->viewDir = $configs[ 'viewDir' ] ?? $this->siteDir . '/views';

        // set properties
        $this->appViewDir = $this->config->get( 'app.appRoot' ) . '/views';
        $this->setLayout( 'main' );
        $this->setPageTitle( $this->config->get( 'app.name' ) );
    }

    public function block()
    {
        ob_start();
    }

    public function blockEnd( string $block = '', string $key = '' )
    {
        $render = ob_get_contents();
        ob_end_clean();

        if ( !$block ) {
            echo $render;
        }

        if ( $key ) {
            $this->$block[ $key ] = $render;
        } else {
            $this->$block[] = $render;
        }
    }

    /**
     * Set view params
     *
     * @param array $params
     */
    public function setParams( array $params )
    {
        $this->params = $params;
    }

    /**
     * Replace or merge view params
     *
     * @param array $params
     */
    public function addParams( array $params )
    {
        $this->params = array_merge( $this->params, $params );
    }

    /**
     * Set layout template name and directory
     *
     * @param string $templateName
     */
    public function setLayout( string $templateName )
    {
        $this->layoutName = 'layouts/' . $templateName;
        $template = $this->viewDir . '/' . $this->layoutName . '.php';

        if ( !file_exists( $template ) ) {
            $template = $this->appViewDir . '/' . $this->layoutName . '.php';
            if ( !file_exists( $template ) ) {
                throw new \Error( 'Template file ' . $template . ' not exist!' );
            }
        }

        $this->layout = $template;
    }

    public function setHtmlClass( array $htmlClass )
    {
        if ( is_array( $htmlClass ) && $htmlClass ) {
            $this->htmlClass = $htmlClass;
        }
    }

    public function addHtmlClass( string $htmlClass )
    {
        $this->htmlClass[] = $htmlClass;
    }

    public function getHtmlClass()
    {
        return implode( $this->htmlClass, ' ' );
    }

    /**
     * Set head content
     *
     * @param string $content
     */
    public function setHeadContent( string $content )
    {
        $this->headContent = [];
        $this->headContent[] = $content;
    }

    public function addHeadContent( string $content )
    {
        $this->headContent[] = $content;
    }

    public function getHeadContent()
    {
        return implode( $this->headContent, '' );
    }

    public function setFootContent( string $content )
    {
        $this->footContent = [];
        $this->footContent[] = $content;
    }

    public function addFootContent( string $content )
    {
        $this->footContent[] = $content;
    }

    public function getFootContent()
    {
        return implode( $this->footContent, '' );
    }

    public function setBodyClass( array $bodyClass )
    {
        if ( is_array( $bodyClass ) && $bodyClass ) {
            $this->bodyClass = $bodyClass;
        }
    }

    public function addBodyClass( string $bodyClass )
    {
        $this->bodyClass[] = $bodyClass;
    }

    public function getBodyClass()
    {
        return implode( $this->bodyClass, ' ' );
    }

    public function setPageTitle( string $pageTitle )
    {
        $this->pageTitle = [];
        $this->pageTitle[] = $pageTitle;
    }

    public function addPageTitle( string $pageTitle )
    {
        $this->pageTitle[] = $pageTitle;
    }

    public function getPageTitle( string $delimiter = ' - ' )
    {
        $titles = $this->pageTitle;
        krsort( $titles );
        return implode( $titles, $delimiter );
    }

    public function setPageDescription( string $pageDescription )
    {
        $this->pageDescription = [];
        $this->pageDescription[] = $pageDescription;
    }

    public function addPageDescription( string $pageDescription )
    {
        $this->pageDescription[] = $pageDescription;
    }

    public function getPageDescription( string $delimiter = '. ' )
    {
        return trim( implode( $this->pageDescription, $delimiter ) );
    }

    public function setBreadcrumb( array $breadcrumb )
    {
        $this->breadcrumb = [];
        $this->breadcrumb = array_merge( $this->breadcrumb, $breadcrumb );
    }

    public function addBreadcrumb( string $key, string $label, string $url )
    {
        $this->breadcrumb[ $key ] = [
            'label' => $label,
            'url' => $url
        ];
    }

    public function getBreadcrumb()
    {
        return $this->breadcrumb;
    }

    public function getBlockHead()
    {
        return implode( '', $this->blockHead );
    }

    public function getBlockBodyStart()
    {
        return implode( '', $this->blockBodyStart );
    }

    public function getBlockBodyEnd()
    {
        return implode( '', $this->blockBodyEnd );
    }

    public function render( string $templateName )
    {
        $template = $this->viewDir . '/' . $templateName . '.php';
        if ( !file_exists( $template ) ) {
            $template = $this->appViewDir . '/' . $templateName . '.php';
            if ( !file_exists( $template ) ) {
                throw new \Error( 'Template file ' . $template . ' not exist!' );
            }
        }

        // render content first
        $content = $this->staticRender( $templateName, $this->getData() );

        // render content with layout via content param
        return $this->staticRender( $this->layoutName, array_merge( $this->getData(), [
            'content' => $content
        ] ) );
    }

    public function getData()
    {
        return [
            'config' => $this->config,
            'params' => $this->params,
            'layout' => $this->layout,
            'headContent' => $this->getHeadContent(),
            'footContent' => $this->getFootContent(),
            'bodyClass' => $this->getBodyClass(),
            'pageTitle' => $this->getPageTitle(),
            'pageDescription' => $this->getPageDescription(),
            'breadcrumb' => $this->getBreadcrumb(),
            'htmlClass' => $this->getHtmlClass()
        ];
    }

    public function staticRender( string $templateName, array $params = [] )
    {
        if ( !isset( $this->templates[ $templateName ] ) ) {

            $modeTemplateName = '';
            $mode = !$this->adaptizer->isDesktop ? $this->adaptizer->getMode() : '';
            $paths = explode( '/', $templateName );
            $lastKey = count( $paths ) - 1;
            $paths[ $lastKey ] = ( $mode ? $mode . '/' : '' ) . $paths[ $lastKey ];
            $modeTemplateName = implode( '/', $paths );

            $template = $this->viewDir . '/' . $modeTemplateName . '.php';
            if ( !file_exists( $template ) ) {
                $template = $this->appViewDir . '/' . $modeTemplateName . '.php';
                if ( !file_exists( $template ) ) {
                    $template = $this->viewDir . '/' . $templateName . '.php';
                    if ( !file_exists( $template ) ) {
                        $template = $this->appViewDir . '/' . $templateName . '.php';
                        if ( !file_exists( $template ) ) {
                            throw new \Error( 'Template file ' . $template . ' not exist!' );
                        }
                    }
                }
            }
            $this->templates[ $templateName ] = $template;
        }

        $template = $this->templates[ $templateName ];
        $params = array_merge( $params, [
            'template' => $template
        ] );

        extract( $params, EXTR_SKIP );

        ob_start();
        require( $template );
        $renderOutput = ob_get_contents();
        ob_end_clean();

        return $renderOutput;
    }

    public function getParams( string $key = '', $default = null )
    {
        if ( $key ) {
            return data_get( $this->params, $key ) ?: $default;
        }

        return $default;
    }

    /**
     * To be use to render REST responses
     *
     * @param array $array
     * @param int $httpResponseCode
     */
    public function renderAsJson( array $array = [], int $httpResponseCode = 200 )
    {
        http_response_code( $httpResponseCode );
        header( 'Content-Type: application/json' );
        echo json_encode( $array, JSON_UNESCAPED_SLASHES );
    }

    public function isAjax()
    {
        return ( !empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) === 'xmlhttprequest' );
    }

    /**
     * Make pagination data
     *
     * @param  string  $uri
     * @param  integer $current         page number
     * @param  int     $list            total item showing/found
     * @param  int     $pages           total available pages
     * @param  int     $total           total available items
     *
     * @return array
     */
    public function makePagination( string $uri, int $current = 1, int $list, int $pages, int $total ) : array
    {
        $pagination = [];
        $current = $current ?: 1;

        $pagination['next'] = [
            'disabled' => ( $current >= $pages  ),
            'url' => $uri . '?page=' . ( $current + 1 )
        ];
        $pagination['prev'] = [
            'disabled' => ( $current === 1 ),
            'url' => $uri . '?page=' . ( $current - 1 )
        ];

        $pagination[ 'current' ] = $current;
        $pagination[ 'pages' ] = $pages;
        $pagination[ 'total' ] = $total;
        $pagination[ 'list' ] = $list;
        $pagination[ 'page' ] = $current;

        return $pagination;
    }
}
