<?php

return [
    'name' => 'SSZ1 Portal',
    'baseUrl' => getenv( 'SITE_BASE_URL' ) ?: 'http://ssz1.local',
    'root' => realpath( __DIR__ . '/../' )
];
