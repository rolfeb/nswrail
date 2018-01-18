<?php

# public configuration information
$public_config = array(
    # 'website'       => 'nswrail.net',
    # 'website-url'   => 'http://nswrail.net',

    'website'       => 'nswrail-dev',
    'website-url'   => 'https://nswrail-dev.local',

    # email settings
    'email-admin'   => 'admin@nswrail.net',
    'email-noreply' => 'noreply@nswrail.net',
);

# private configuration information
$secure_dir = $_SERVER['DOCUMENT_ROOT'] . '/../secure';
$secure_config = json_decode(file_get_contents($secure_dir . '/config.json'), true);

$config = array_merge($public_config, $secure_config);

function get_config($key)
{
    global $config;

    return $config[$key];
}

?>
