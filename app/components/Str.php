<?php

namespace app\components;

class Str
{
    public function slugify( string $str, string $delimiter = '-' ) : string
    {
        return str_slug($str, $delimiter);
    }

    public function removeDoubleSpaces( string $str ) : string
    {
        $str = preg_replace( '!\s+!', ' ', $str );
        return trim( $str );
    }

    public function removeTexts( string $str ) : string
    {
        $str = preg_replace( '/([A-Za-z])\w+/', '', $str );
        return trim( $str );
    }
}
