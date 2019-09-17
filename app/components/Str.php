<?php

namespace app\components;

use Illuminate\Support\Str as BaseStr;

class Str extends BaseStr
{
    public static function removeDoubleSpaces( string $str ) : string
    {
        $str = preg_replace( '!\s+!', ' ', $str );
        return trim( $str );
    }

    public static function removeTexts( string $str ) : string
    {
        $str = preg_replace( '/([A-Za-z])\w+/', '', $str );
        return trim( $str );
    }
}
