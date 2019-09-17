<?php

return [
    'baseUrl' => 'http://ssz1portal.local',
    'appRoot' => realpath( __DIR__ . '/../' ),
    'milkyway' => realpath( __DIR__ . '/../../' ),
    'timezone' => 'Asia/Kuala_Lumpur',
    'envyronment' => getenv( 'envyronment' ) ?: 'development'
];
