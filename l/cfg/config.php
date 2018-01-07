<?php

function get_config($key)
{
    $config = array(
        'website'       => 'nswrail-dev',
        'website-url'   => 'http://nswrail-dev/',

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
