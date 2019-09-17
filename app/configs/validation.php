<?php

/**
 * HTML Validations
 *
 * @see http://html5pattern.com/Names
 * @var array
 */
return [

    // Starts with 01, min 10 characters and max 11 characters
    'phoneNumber' => '(01)[0-9](\d{7}|\d{8})',

    // LowerCase and min 15 characters
    'username' => '^[a-z0-9_]{1,15}$',

    // UpperCase, LowerCase, Number/SpecialChar and min 8 characters
    'password' => '(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$',

    // Password (UpperCase, LowerCase and Number)
    'passwordSimple' => '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$',
];
