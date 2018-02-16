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

#
# Manage settings. These are parameters that are not really site-specific.
#
function get_setting($key)
{
    $settings = [
        # how old a photo has to be before we consider it "historic"
        'HISTORIC_PHOTO_YEARS' => 30,

        # max age in weeks we consider a photo upload as recent
        'RECENT_PHOTO_UPLOAD_WEEKS' => 4,
    ];

    return $settings[$key];
}

?>
