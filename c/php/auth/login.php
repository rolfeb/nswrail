<?php

require_once 'site.inc';

// XXX: validate args (especially missing)

$username = quote_external($_POST["username"]);
$password = quote_external($_POST["password"]);
$referer = quote_external($_SERVER["HTTP_REFERER"]);

header("Cache-control: private");

if (!$user->login($username, $password))
{
    error_page("Login failed $username / $password", $referer);
    exit();
}

session_write_close();

header("Location: $referer");

?>
