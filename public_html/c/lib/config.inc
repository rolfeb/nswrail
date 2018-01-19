<?php

function load_config()
{
    global $config;

    $config = json_decode(file_get_contents($_SERVER['NSWRAIL_CONFIG_FILE']), true);
}

function get_config($key)
{
    global $config;

    return $config[$key];
}

?>
