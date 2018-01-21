<?php

# third party packages
if (version_compare(PHP_VERSION, "5.6") == -1) {
    require "thirdparty/password_compat/lib/password.php";
}

require "HTML/Template/ITX.php";
require "HTML/BBCodeParser.php";

require 'thirdparty/PHPMailer/src/Exception.php';
require 'thirdparty/PHPMailer/src/PHPMailer.php';
require 'thirdparty/PHPMailer/src/SMTP.php';


# classes
require "user.php";
require "audit.inc";

# other functions
require "config.inc";
require "dbutil.inc";
require "dbutil_line.inc";
require "error.inc";
require "init.inc";
require "page_header.inc";
require "page_utils.inc";
require "page_utils_main.inc";
require "util.inc";
require "r_location.php";

?>
