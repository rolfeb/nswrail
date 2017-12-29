<?php

require_once 'site.inc';

$referer = $_SERVER["HTTP_REFERER"];

header("Cache-control: private");

unset($_SESSION["username"]);
unset($_SESSION["role"]);
unset($_SESSION["uid"]);
unset($_SESSION["fullname"]);

header("Location: $referer");

?>
