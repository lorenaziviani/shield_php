<?php
 
return [
    'env' => getenv('APP_ENV') ?: 'dev',
    'debug' => getenv('APP_DEBUG') === 'true',
    'log_file' => __DIR__ . '/logs/shieldphp.log',
]; 