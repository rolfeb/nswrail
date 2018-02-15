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
    # XXX: validate args (especially missing)
    $username = quote_external($_POST["username"]);
    $password = quote_external($_POST["password"]);
    $referer = quote_external($_SERVER["HTTP_REFERER"]);

    header("Cache-control: private");

    try_login($username, $password, $_SERVER['REMOTE_ADDR']);

    session_write_close();

    header("Location: $referer");
} catch (Exception $e) {
    report_error($e);
}

?>
