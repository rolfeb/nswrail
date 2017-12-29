<?php

require_once 'site.inc';

$username = quote_external($_POST["username"]);
$password = quote_external($_POST["password"]);
$referer = quote_external($_SERVER["HTTP_REFERER"]);

header("Cache-control: private");

$person = get_person_by_email($username);

if (!$person)
{
    error_page("Login failed", $referer);
    exit();
}

$enc_password = auth_encrypt_password($password, $person['pwdsalt']);
if ($enc_password != $person['password'])
{
    error_page("Login failed", $referer);
    exit();
}

if ($person['status'] != 'active')
{
    error_page("Login denied", $referer);
    exit();
}

$_SESSION['username'] = $username;
$_SESSION['role'] = $person['role'];
$_SESSION['uid'] = $person['uid'];
if ($person['anonymous'] == 'Y')
    $_SESSION['fullname'] = sprintf('User %d', $person['uid']);
else
    $_SESSION['fullname'] = $person['fullname'];

session_write_close();

header("Location: $referer");

?>
