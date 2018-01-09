<?php

function get_config($key)
{
    $config = array(
        'website'       => 'nswrail-dev.local',
        'website-url'   => 'http://nswrail-dev.local/',

        // email settings
        'email-admin'   => 'admin@nswrail.net',
        'email-noreply' => 'noreply@nswrail.net',

        'smtp-server'   => 'mail-hub.bigpond.net.au',
        'smtp-issecure' => false,
        'smtp-username' => 'user@example.com',
        'smtp-password' => 'secret',
    );
}

?>
