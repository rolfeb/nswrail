<?php

require 'site.inc';

function try_login($username, $password, $remote_ip)
{
    global $user;

    $user->login($username, $password, $remote_ip);

    Audit::addentry(Audit::A_LOGIN);

    return NULL;
}

try {
    $username = param_post_string("username");
    $password = param_post_string("password"];
    $referer = $_SERVER["HTTP_REFERER"];
    if (!isset($referer)) {
        $referer = get_config('website-url');
    }

    header("Cache-control: private");

    try_login($username, $password, $_SERVER['REMOTE_ADDR']);

    session_write_close();

    header("Location: $referer");
} catch (Exception $e) {
    report_error($e);
}

?>
