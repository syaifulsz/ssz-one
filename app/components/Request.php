<?php

namespace app\components;

class Request
{
    public $httpRequestMethod;
    public $inputData = [];

    public function __construct()
    {
        $this->setHttpRequestMethod();
        $this->setInputData();
    }

    protected function setHttpRequestMethod()
    {
        $this->httpRequestMethod = $this->validateHttpRequestMethod( $_SERVER[ 'REQUEST_METHOD' ] );
    }

    protected function validateHttpRequestMethod( string $input )
    {
        if ( empty( $input ) ) {
            throw new InvalidArgumentException('I need valid value');
        }

        if ( in_array( $input, [
            'GET',
            'POST',
            'PUT',
            'FILE',
        ] ) ) {
            return $input;
        }

        throw new InvalidArgumentException('Unexpected value.');
    }

    protected function setInputData()
    {
        switch ( $this->httpRequestMethod ) {

            case 'GET' :
                $this->setDataFromGet();
                break;

            case 'POST' :
                $this->setDataFromPost();
                break;

            case 'PUT' :
                $this->setDataFromPut();
                break;

            case 'FILE' :
                $this->setDataFromFile();
                break;

            default :
                throw new Exception( 'Unmapped httpActionMethod. Value provided: ' . $this->httpRequestMethod );
        }
    }

    protected function setDataFromGet()
    {
        $this->inputData = array_merge( $this->inputData, $_GET );
    }

    protected function setDataFromPost()
    {
        $this->inputData = array_merge( $this->inputData, $_POST );
    }

    protected function setDataFromPut()
    {
        $this->inputData = array_merge( $this->inputData, $_PUT );
    }

    protected function setDataFromFile()
    {
        $this->inputData = array_merge( $this->inputData, $_FILE );
    }

    public function get( string $key, $defaultValue = null )
    {
        if ( !empty( $this->inputData[ $key ] ) ) {
            return $this->inputData[ $key ];
        }

        return $defaultValue;
    }
}
