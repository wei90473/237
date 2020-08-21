<?php
if ( ! empty($_SERVER['HTTP_CLIENT_IP'])) {

    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} elseif(isset($_SERVER['REMOTE_ADDR'])) {

    $ip = $_SERVER['REMOTE_ADDR'];
} else {

    $ip = '';
}

$passIP = config('app.pass_ip');

$ipCheck = (is_array($passIP) && in_array($ip, $passIP))? true : false ;

return [
    'enabled' => (env('APP_DEBUG') === true),
    'showBar' => env('APP_ENV') !== 'production',
    'accepts' => [
        'text/html',
    ],
    'appendTo' => 'body',
    'editor' => 'subl://open?url=file://%file&line=%line',
    'maxDepth' => 4,
    'maxLength' => 1000,
    'scream' => true,
    'showLocation' => true,
    'strictMode' => true,
    'editorMapping' => [],
    'panels' => [
        'routing' => true,
        'database' => true,
        'view' => true,
        'event' => false,
        'session' => true,
        'request' => true,
        'auth' => true,
        'html-validator' => false,
        'terminal' => true,
    ],
];
