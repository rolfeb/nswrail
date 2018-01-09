<?php

require_once 'site.inc';

function logout()
{
    global $user;

    Audit::addentry(Audit::A_LOGOUT);

    $user->logout();
}

header("Cache-control: private");

logout();

$referer = $_SERVER["HTTP_REFERER"];
header("Location: $referer");

?>
