<?php
global $BASE_PHP_PATH;

require_once "HTML/Template/ITX.php";
require_once "HTML/BBCodeParser.php";

require_once "config.inc";

require_once "error.inc";
require_once "audit.inc";
require_once "util.inc";
require_once "auth.inc";
require_once "page_utils.inc";

require_once "init.inc";

require_once "r_person.php";
require_once "r_location.php";
require_once "page_header.inc";
require_once "dbutil.inc";

# third party packages
if (version_compare(PHP_VERSION, "5.6") == -1) {
    require "thirdparty/password_compat/lib/password.php";
}

require 'thirdparty/PHPMailer/src/Exception.php';
require 'thirdparty/PHPMailer/src/PHPMailer.php';
require 'thirdparty/PHPMailer/src/SMTP.php';

?>
