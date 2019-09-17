<?php

require __DIR__ . '/../../../vendor/autoload.php';

app\Bootstrap::getInstance( [
    'siteId' => 'ssz1portal',
    'errorLogFile' => __DIR__ . '/../runtime/logs/app.log'
] );
