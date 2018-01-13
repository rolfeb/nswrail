<?php

require_once 'site.inc';

function try_login($username, $password, $remote_ip)
{
    global $user;

    if (!$user->login($username, $password, $remote_ip))
        return "Login failed $username / $password";

    Audit::addentry(Audit::A_LOGIN);

    return NULL;
}

// XXX: validate args (especially missing)
$username = quote_external($_POST["username"]);
$password = quote_external($_POST["password"]);
$referer = quote_external($_SERVER["HTTP_REFERER"]);

header("Cache-control: private");

$errormsg = try_login($username, $password, $_SERVER['REMOTE_ADDR']);
if ($errormsg) {
    error_page($errormsg, $referer);
}

session_write_close();

header("Location: $referer");

?>
