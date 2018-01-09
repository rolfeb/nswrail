<?php

require_once 'site.inc';

function try_login($username, $password)
{
    global $user;

    if (!$user->login($username, $password))
        return "Login failed $username / $password";

    Audit::addentry(Audit::A_LOGIN);

    return NULL;
}

// XXX: validate args (especially missing)
$username = quote_external($_POST["username"]);
$password = quote_external($_POST["password"]);
$referer = quote_external($_SERVER["HTTP_REFERER"]);

header("Cache-control: private");

$errormsg = try_login($username, $password);
if ($errormsg) {
    error_page($errormsg, $referer);
}

session_write_close();

header("Location: $referer");

?>
