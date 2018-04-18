<?php

require_once 'site.inc';

function logout()
{
    /** @var User $user */
    global $user;

    Audit::addentry(Audit::A_LOGOUT);

    $user->logout();
}
try {
    header("Cache-control: private");

    logout();

    $referer = $_SERVER["HTTP_REFERER"];
    header("Location: $referer");
} catch (Exception $e) {
    report_error($e);
}
