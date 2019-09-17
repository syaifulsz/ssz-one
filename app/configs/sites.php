<?php

$baseRoot = realpath( __DIR__ . '/../../' );

return [
    'base' => $baseRoot,
    'app' => realpath( __DIR__ . '/../' ),
    'root' => realpath( $baseRoot . '/sites/' )
];
